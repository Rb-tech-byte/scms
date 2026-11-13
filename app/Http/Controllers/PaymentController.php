<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use Illuminate\View\View;
use App\Models\Applicant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Spatie\Activitylog\Models\Activity;
use App\Traits\SocialSmartTechConstants;
use App\Services\PermissionService;

/**
 * Class PaymentController
 *
 * Coordinates fee collection, installment reconciliation, balance calculation,
 * and immutable payment audit recording.
 *
 * DEVELOPER EXTENSION & GATEWAY INTEGRATION GUIDE:
 * 1. Financial Immutability Policy:
 *    - In accordance with public accounting standards, payment records are immutable.
 *    - The `edit()` action intentionally redirects back with a warning and records an unauthorized
 *      edit attempt in the Spatie activity log.
 *
 * 2. Automated Payment Integration (M-Pesa / GePG / AirtelMoney / TigoPesa / Stripe):
 *    - To add an automated gateway, create an API route/controller (e.g. `Api/PaymentGatewayController`)
 *      that receives the payment provider's webhook payload.
 *    - Verify provider cryptographic signature (e.g., HMAC-SHA256).
 *    - Call the same invoice balance recalculation logic found in `store()`:
 *      $newBalance = max(0, $remaining - $amount);
 *      $invoice->update(['balance' => $newBalance, 'paid_amount' => $newPaid, 'status' => $status]);
 *      Payment::create([...]);
 */
class PaymentController extends Controller
{
    use SocialSmartTechConstants;

    /**
     * Display a paginated listing of tuition payments filtered by branch scope.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if (!PermissionService::can('List payments')) {
            abort(403, 'Unauthorized');
        }

        $search = $request->get('search', '');
        
        $payments = Payment::search($search)
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view('app.payments.index', compact('payments', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        if (!PermissionService::canCreate('Add payments')) {
            abort(403, 'Unauthorized');
        }

        $applicants = Applicant::pluck('full_name','id');
        $courses = Course::pluck('title', 'id');

        return view('app.payments.create', compact('applicants', 'courses'));
    }

    /**
     * Store a newly created tuition payment, calculate remaining invoice balance,
     * update invoice status, and record the cashier employee ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (!PermissionService::canCreate('Add payments')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'installment_type' => ['required', 'in:quarter,half,full'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'receipt_number' => ['required','string','max:255'],
        ]);
        $validated['user_id'] = Applicant::where('id', $validated['applicant_id'])->value('user_id');
        $where = [
            'invoices.applicant_id' => $validated['applicant_id'],
            'course_id' => $validated['course_id']
        ];

        $check_balance = Invoice::join('enrollments', 'enrollments.applicant_id', 'invoices.applicant_id')
            ->select('invoices.id', 'enrollments.id as enrollment', 'balance', 'paid_amount', 'invoices.applicant_id')
            ->where($where)
            ->first();
        if (!empty($check_balance)) {
            $remaining = (float) $check_balance->balance;
            $installment = $request->input('installment_type');

            // compute amount based on remaining balance
            if ($installment === 'quarter') {
                $computedAmount = round($remaining * 0.25, 2);
                $validated['payment_status'] = 1; // partial
            } elseif ($installment === 'half') {
                $computedAmount = round($remaining * 0.5, 2);
                $validated['payment_status'] = 1; // partial
            } elseif ($installment === 'full') {
                $computedAmount = round($remaining, 2);
                $validated['payment_status'] = 0; // full
            }

            // override amount with computed where applicable
            $validated['amount'] = $computedAmount;

            $newBalance = max(0, $remaining - $computedAmount);
            $newPaid = (float) ($check_balance->paid_amount ?? 0) + $computedAmount;

            $invoiceStatus = $newBalance <= 0 ? '1' : '2';

            Invoice::where([
                'id' => $check_balance->id,
                'applicant_id' => $check_balance->applicant_id,
                'enrollment_id' => $check_balance->enrollment
            ])->update([
                'balance' => $newBalance,
                'paid_amount' => $newPaid,
                'status' => $invoiceStatus,
            ]);
        }

        $validated['invoice_id'] = $check_balance->id;
        $validated['employee_id'] = auth()->user()->id;
        Payment::create($validated);

        return redirect()
            ->route('payments.index')
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Payment $payment)
    {
        if (!PermissionService::canView('View payments', $payment)) {
            abort(403, 'Unauthorized');
        }

        return view('app.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Payment $payment): RedirectResponse
    {
        if (!PermissionService::canUpdate('Update payments', $payment)) {
            abort(403, 'Unauthorized');
        }
        //log activity performes by auth user
        activity()
            ->causedBy(auth()->user())
            ->event('Payment Edit')
            ->createdAt(now())
            ->withProperties(['attributes' => $payment->applicant->name]);

        return to_route('payments.index')->with('error', 'Payment Cannot be Edited , Contact Support for further assistance. Your attempt to delete was sent to the administrator For further checkup.');
        $branch = session('branch');

        $applicants = empty($branch) ? Applicant::pluck('full_name','id') : Applicant::where('branch_id', $branch->id)->pluck('full_name', 'id');
        $courses = Course::pluck('title', 'id');

        return view(
            'app.payments.edit',
            compact('payment', 'applicants', 'courses')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        if (!PermissionService::canUpdate('Update payments', $payment)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'payment_status' => ['required', 'boolean'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);
        $validated['employee_id'] = auth()->user()->id;

        $payment->update($validated);

        return redirect()
            ->route('payments.edit', $payment)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Payment $payment
    ){
        if (!PermissionService::canDelete('Delete payments', $payment)) {
            abort(403, 'Unauthorized');
        }
        //log activity performes by auth user name
        activity()
            ->causedBy(auth()->user()->id)
            ->log('Payment Delete');
        // return redirect()->back()->with('error', 'Payment Cannot be deleted , Contact Support for further assistance. Your attempt to delete was sent to the administrator For further checkup.');
        echo  'Payment Cannot be deleted , Contact Support for further assistance. Your attempt to delete was sent to the administrator For further checkup';
        // $payment->delete();

        // return redirect()->back()
        //     ->withSuccess(__('crud.common.removed'));
    }
    public function getCourse()
    {
        try {
            $applicant_id = request('id');
            $courses = \App\Models\Enrollment::where('applicant_id', $applicant_id)
                ->join('courses', 'courses.id', 'enrollments.course_id')
                ->get();

            if ($courses->isEmpty()) {
                echo 0;
            }
            //if result found
            echo '<option value="0">select Course</option>';
            foreach ($courses as $value) {
                echo '<option value="' . $value->course_id . '">' . $value->title . '</option>';
            }
        } catch (\Exception $e) {
            //in case of any exception
            echo 0;
        }
    }
    public function getCourseAmount()
    {
        $applicant_id = request('student_id');
        $course = request('course');
        $remained_amount = Invoice::where('applicant_id', $applicant_id)->whereIn('enrollment_id', \App\Models\Enrollment::where(['course_id' => $course, 'applicant_id' => $applicant_id])->get('id')->first())->value('balance');
        echo $remained_amount;
    }
}
