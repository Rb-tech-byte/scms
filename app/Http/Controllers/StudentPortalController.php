<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Venue;
use App\Services\PermissionService;
use App\Services\SrsWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Handles student self-service workflows (SRS R1):
 * - Course catalog browsing and self-enrollment
 * - Unenrolling unpaid enrollments
 * - Control number generation and payment receipt upload
 * - Official printable payment receipt view
 */
class StudentPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'branch.access']);
    }

    /**
     * Show available courses, current enrollments, invoices, and payment receipts for the student.
     */
    public function index(): View
    {
        $user = Auth::user();
        $applicant = Applicant::where('user_id', $user->id)->first();

        $courses = Course::orderBy('title')->get();
        $venues = Venue::with('branch')->orderBy('name')->get();
        $enrollments = $applicant
            ? Enrollment::with(['course', 'venue.branch', 'invoices.payments', 'certificate'])
                ->where('applicant_id', $applicant->id)
                ->latest()
                ->get()
            : collect();

        $overdueSchedules = $applicant
            ? PaymentSchedule::with(['invoice.enrollment.course'])
                ->whereHas('invoice', fn ($q) => $q->where('applicant_id', $applicant->id))
                ->whereIn('status', ['pending', 'overdue'])
                ->whereDate('due_date', '<', Carbon::today())
                ->get()
            : collect();

        return view('student.portal', compact('user', 'applicant', 'courses', 'venues', 'enrollments', 'overdueSchedules'));
    }

    /**
     * Self-enroll authenticated student into a course and automatically generate an invoice.
     */
    public function enroll(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'venue_id' => ['required', 'exists:venues,id'],
            'session' => ['required', 'in:morning,afternoon'],
            'start_date' => ['required', 'date'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $venue = Venue::findOrFail($validated['venue_id']);

        $applicant = Applicant::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $user->name,
                'country_id' => Country::value('id') ?? 1,
                'mobile' => $user->phone ?: '0700000000',
                'email' => $user->email,
                'education_level' => '5',
                'home_address' => $user->address,
                'branch_id' => $venue->branch_id ?? Branch::value('id') ?? 1,
            ]
        );

        $alreadyEnrolled = Enrollment::where('applicant_id', $applicant->id)
            ->where('course_id', $course->id)
            ->where('enrollment_status', 'active')
            ->exists();

        if ($alreadyEnrolled) {
            return back()->with('warning', 'You are already actively enrolled in this course.');
        }

        $startDate = Carbon::parse($validated['start_date']);
        $weeks = max(1, (int) $course->duration);
        $endDate = $startDate->copy()->addWeeks($weeks);

        DB::transaction(function () use ($applicant, $course, $venue, $validated, $startDate, $endDate, $user) {
            $enrollment = Enrollment::create([
                'course_id' => $course->id,
                'venue_id' => $venue->id,
                'session' => $validated['session'],
                'applicant_id' => $applicant->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'enrollment_status' => 'active',
                'fee' => $course->fee,
                'duration' => (string) $course->duration,
            ]);

            $discount = $course->has_discount ? (float) ($course->discount ?? 0) : 0.0;
            $netFee = max(0.0, (float) $course->fee - $discount);

            Invoice::create([
                'applicant_id' => $applicant->id,
                'enrollment_id' => $enrollment->id,
                'balance' => $netFee,
                'total_amount' => $netFee,
                'paid_amount' => 0,
                'description' => "Self-enrollment for {$course->title}",
                'invoice_prefix' => generateInvoicePrefix(),
                'has_discount' => $discount > 0 ? 1 : 0,
                'discount_amount' => $discount,
                'date' => now()->toDateString(),
                'due_date' => $startDate->copy()->addDays(14)->toDateString(),
                'status' => $netFee <= 0 ? '1' : '0',
            ]);

            activity('enrollment')
                ->causedBy($user)
                ->performedOn($enrollment)
                ->log("Student enrolled in {$course->title}");
        });

        return redirect()->route('student.portal')->with('success', 'Course enrollment confirmed and invoice generated.');
    }

    /**
     * Allow a student to unenroll from a course if no tuition payments have been recorded.
     */
    public function unenroll(Enrollment $enrollment): RedirectResponse
    {
        $user = Auth::user();
        $applicant = Applicant::where('user_id', $user->id)->first();

        if (!$applicant || (int) $enrollment->applicant_id !== (int) $applicant->id) {
            abort(403, 'Unauthorized');
        }

        $paidTotal = (float) $enrollment->invoices()->sum('paid_amount');
        if ($paidTotal > 0) {
            return back()->with('error', 'Cannot unenroll after tuition payments have been recorded.');
        }

        DB::transaction(function () use ($enrollment, $user) {
            foreach ($enrollment->invoices()->get() as $invoice) {
                $invoice->paymentSchedules()->delete();
                $invoice->delete();
            }
            $courseTitle = $enrollment->course?->title ?? 'Course';
            $enrollment->delete();

            activity('enrollment')
                ->causedBy($user)
                ->log("Student unenrolled from {$courseTitle}");
        });

        return back()->with('success', 'Course unenrollment completed.');
    }

    /**
     * Generate an institutional control number for an invoice (student-owned or authorized staff).
     */
    public function generateControlNumber(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoiceAccess($invoice);

        $controlNumber = SrsWorkflowService::ensureControlNumber($invoice);

        activity('billing')
            ->causedBy(Auth::user())
            ->performedOn($invoice)
            ->log("Generated Control Number {$controlNumber}");

        return back()->with('success', "Control Number generated: {$controlNumber}");
    }

    /**
     * Display receipt upload and payment submission form for a student invoice.
     */
    public function showPaymentForm(Invoice $invoice): View|RedirectResponse
    {
        $this->authorizeInvoiceAccess($invoice);

        if ((float) $invoice->balance <= 0) {
            return redirect()->route('student.portal')->with('info', 'This invoice is already fully paid.');
        }

        $controlNumber = SrsWorkflowService::ensureControlNumber($invoice);
        $invoice->load(['enrollment.course', 'enrollment.venue.branch', 'payments']);

        return view('student.pay', compact('invoice', 'controlNumber'));
    }

    /**
     * Submit student payment details and securely upload receipt evidence.
     */
    public function submitPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoiceAccess($invoice);

        $maxPayable = max(1, (float) $invoice->balance);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', "max:{$maxPayable}"],
            'payment_method' => ['required', 'in:control_number,bank_transfer,mobile_money,cash'],
            'transaction_reference' => ['required', 'string', 'max:100'],
            'receipt_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $file = $request->file('receipt_file');
        $allowedExtensions = ['pdf', 'jpeg', 'png', 'jpg'];
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            return back()->with('error', 'Invalid receipt file format. Allowed: PDF, JPG, PNG.');
        }

        $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
        $filename = 'receipt_' . $invoice->id . '_' . time() . '_' . Str::random(8) . '.' . $safeExt;
        $targetDir = storage_path('app/public/receipts');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }
        $file->move($targetDir, $filename);

        SrsWorkflowService::ensureControlNumber($invoice);

        $payment = SrsWorkflowService::applyPaymentToInvoice($invoice, [
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'],
            'receipt' => $filename,
            'receipt_file' => $filename,
            'verification_status' => 'pending',
            'user_id' => Auth::id(),
            'date' => now(),
        ]);

        activity('billing')
            ->causedBy(Auth::user())
            ->performedOn($payment)
            ->log("Student submitted payment receipt {$payment->receipt_number}");

        return redirect()->route('payments.receipt', $payment)
            ->with('success', "Payment and receipt uploaded (Ref: {$payment->receipt_number}).");
    }

    /**
     * Render printable payment receipt for student or authorized finance staff.
     */
    public function viewReceipt(Payment $payment): View
    {
        $user = Auth::user();
        $isOwner = $payment->applicant && (int) $payment->applicant->user_id === (int) $user->id;

        if (!$isOwner && !PermissionService::can('list-payments') && !PermissionService::can('list-invoices')) {
            abort(403, 'Unauthorized');
        }

        $payment->load(['applicant', 'course', 'invoice.enrollment.venue.branch']);
        return view('payments.receipt', compact('payment'));
    }

    private function authorizeInvoiceAccess(Invoice $invoice): void
    {
        $user = Auth::user();
        $isOwner = $invoice->applicant && (int) $invoice->applicant->user_id === (int) $user->id;
        if (!$isOwner && !PermissionService::can('list-invoices')) {
            abort(403, 'Unauthorized');
        }
    }
}

