<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Invoice;
use App\Models\PaymentSchedule;
use App\Models\Applicant;
use App\Services\PaymentScheduleService;
use Illuminate\Support\Carbon;

class PaymentScheduleController extends Controller
{
    protected PaymentScheduleService $scheduleService;

    public function __construct(PaymentScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display payment schedules index
     */
    public function index(Request $request): View
    {
        $query = PaymentSchedule::with(['invoice.applicant'])
            ->orderBy('due_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by applicant
        if ($request->filled('applicant_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('applicant_id', $request->applicant_id);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $schedules = $query->paginate(20);

        // Get statistics
        $statistics = [
            'total' => PaymentSchedule::count(),
            'pending' => PaymentSchedule::pending()->count(),
            'overdue' => PaymentSchedule::overdue()->count(),
            'paid' => PaymentSchedule::paid()->count(),
            'due_soon' => PaymentSchedule::dueSoon(7)->count(),
        ];

        // Get invoices with balances for dropdown
        $invoices = Invoice::where('balance', '>', 0)
            ->with('applicant')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return view('schedules.index', compact('schedules', 'statistics', 'invoices'));
    }

    /**
     * Show form to generate schedules
     */
    public function create(Request $request): View
    {
        $invoice = null;
        
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with(['applicant', 'paymentSchedules'])
                ->findOrFail($request->invoice_id);
        }

        // Get invoices with balances
        $invoices = Invoice::where('balance', '>', 0)
            ->with('applicant')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('schedules.create', compact('invoices', 'invoice'));
    }

    /**
     * Generate payment schedules
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'template' => ['required', 'in:weekly,biweekly,monthly,quarterly'],
            'installments' => ['required', 'integer', 'min:1', 'max:36'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'custom_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $invoice = Invoice::with('paymentSchedules')->findOrFail($validated['invoice_id']);
            
            // Check if invoice has balance
            $balance = $validated['custom_amount'] ?? (float) $invoice->balance;
            
            if ($balance <= 0) {
                return back()->with('error', 'Invoice has no outstanding balance.');
            }

            // Generate schedules
            $schedules = $this->scheduleService->generateSchedules(
                $invoice,
                $validated['template'],
                $validated['installments'],
                Carbon::parse($validated['start_date']),
                $validated['custom_amount'] ?? null
            );

            return redirect()
                ->route('payment-schedules.show', $invoice->id)
                ->with('success', "Successfully generated {$schedules->count()} payment schedule(s).");
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error generating schedules: ' . $e->getMessage());
        }
    }

    /**
     * Show schedules for a specific invoice
     */
    public function show($invoiceId): View
    {
        $invoice = Invoice::with(['applicant', 'paymentSchedules' => function ($query) {
            $query->orderBy('due_date');
        }])->findOrFail($invoiceId);

        $statistics = $this->scheduleService->getScheduleStatistics($invoice);

        return view('schedules.show', compact('invoice', 'statistics'));
    }

    /**
     * Update a payment schedule
     */
    public function update(Request $request, PaymentSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'due_date' => ['sometimes', 'date'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,sent,paid,overdue,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $schedule->update($validated);

            return back()->with('success', 'Payment schedule updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating schedule: ' . $e->getMessage());
        }
    }

    /**
     * Delete a payment schedule
     */
    public function destroy(PaymentSchedule $schedule): RedirectResponse
    {
        try {
            $invoiceId = $schedule->invoice_id;
            $schedule->delete();

            return redirect()
                ->route('payment-schedules.show', $invoiceId)
                ->with('success', 'Payment schedule deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting schedule: ' . $e->getMessage());
        }
    }

    /**
     * Mark schedule as sent
     */
    public function markAsSent(PaymentSchedule $schedule): RedirectResponse
    {
        try {
            $schedule->markAsSent();
            return back()->with('success', 'Schedule marked as sent.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a schedule
     */
    public function cancel(Request $request, PaymentSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $schedule->cancel($validated['notes'] ?? null);
            return back()->with('success', 'Schedule cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show reminders page
     */
    public function reminders(Request $request): View
    {
        $daysAhead = $request->get('days_ahead', 7);
        
        $schedules = $this->scheduleService->getSchedulesDueForReminder($daysAhead);

        return view('schedules.reminders', compact('schedules', 'daysAhead'));
    }

    /**
     * Send reminders
     */
    public function sendReminders(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_ids' => ['required', 'array'],
            'schedule_ids.*' => ['exists:payment_schedules,id'],
        ]);

        try {
            $schedules = PaymentSchedule::whereIn('id', $validated['schedule_ids'])->get();
            
            $count = $this->scheduleService->markSchedulesAsSent($schedules);

            // TODO: Integrate with notification system (email/SMS)

            return back()->with('success', "Marked {$count} schedule(s) as sent.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
