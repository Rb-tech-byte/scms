<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PaymentScheduleService
{
    /**
     * Generate payment schedules for an invoice
     */
    public function generateSchedules(
        Invoice $invoice,
        string $template,
        int $installments,
        Carbon $startDate,
        ?float $customAmount = null
    ): Collection {
        // Validate invoice has balance
        $balance = $customAmount ?? (float) $invoice->balance;
        
        if ($balance <= 0) {
            throw new \Exception('Invoice has no outstanding balance.');
        }

        if ($installments < 1 || $installments > 36) {
            throw new \Exception('Installments must be between 1 and 36.');
        }

        // Cancel existing pending schedules
        $this->cancelExistingSchedules($invoice);

        // Calculate installment amount
        $perAmount = round($balance / $installments, 2);
        
        // Create schedules
        $schedules = collect();
        $totalAllocated = 0;

        for ($i = 0; $i < $installments; $i++) {
            $dueDate = $this->calculateDueDate($startDate, $template, $i);
            
            // Calculate amount for this installment
            $amount = $perAmount;
            
            // Adjust last installment to account for rounding differences
            if ($i === $installments - 1) {
                $amount = round($balance - $totalAllocated, 2);
            }
            
            $totalAllocated += $amount;

            $schedule = PaymentSchedule::create([
                'invoice_id' => $invoice->id,
                'installment_number' => $i + 1,
                'due_date' => $dueDate,
                'amount' => $amount,
                'status' => PaymentSchedule::STATUS_PENDING,
            ]);

            $schedules->push($schedule);
        }

        return $schedules;
    }

    /**
     * Calculate due date based on template
     */
    private function calculateDueDate(Carbon $startDate, string $template, int $index): Carbon
    {
        return match($template) {
            'weekly' => $startDate->copy()->addWeeks($index),
            'biweekly' => $startDate->copy()->addWeeks($index * 2),
            'monthly' => $startDate->copy()->addMonths($index),
            'quarterly' => $startDate->copy()->addMonths($index * 3),
            default => $startDate->copy()->addMonths($index),
        };
    }

    /**
     * Cancel existing pending schedules for an invoice
     */
    public function cancelExistingSchedules(Invoice $invoice, ?string $reason = null): int
    {
        $count = PaymentSchedule::where('invoice_id', $invoice->id)
            ->whereIn('status', [PaymentSchedule::STATUS_PENDING, PaymentSchedule::STATUS_SENT])
            ->update([
                'status' => PaymentSchedule::STATUS_CANCELLED,
                'notes' => $reason ?? 'Cancelled due to new schedule generation',
            ]);

        return $count;
    }

    /**
     * Update schedule statuses based on due dates
     */
    public function updateOverdueStatuses(): int
    {
        return PaymentSchedule::where('status', PaymentSchedule::STATUS_PENDING)
            ->whereDate('due_date', '<', now())
            ->update(['status' => PaymentSchedule::STATUS_OVERDUE]);
    }

    /**
     * Get schedules due for reminder
     */
    public function getSchedulesDueForReminder(int $daysAhead = 0): Collection
    {
        $endDate = now()->addDays($daysAhead);

        return PaymentSchedule::with(['invoice.applicant'])
            ->whereNull('sent_at')
            ->whereIn('status', [PaymentSchedule::STATUS_PENDING, PaymentSchedule::STATUS_OVERDUE])
            ->whereDate('due_date', '<=', $endDate)
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Mark schedules as sent
     */
    public function markSchedulesAsSent(Collection $schedules): int
    {
        $count = 0;
        
        foreach ($schedules as $schedule) {
            $schedule->markAsSent();
            $count++;
        }

        return $count;
    }

    /**
     * Process payment against schedules
     */
    public function processPaymentAgainstSchedules(Invoice $invoice, $payment): void
    {
        $paymentAmount = (float) $payment->amount;
        
        // Get pending/overdue schedules ordered by due date
        $schedules = PaymentSchedule::where('invoice_id', $invoice->id)
            ->whereIn('status', [PaymentSchedule::STATUS_PENDING, PaymentSchedule::STATUS_OVERDUE, PaymentSchedule::STATUS_SENT])
            ->orderBy('due_date')
            ->get();

        $remainingPayment = $paymentAmount;

        foreach ($schedules as $schedule) {
            if ($remainingPayment <= 0) {
                break;
            }

            $scheduleAmount = (float) $schedule->amount;

            if ($remainingPayment >= $scheduleAmount) {
                // Full payment of this schedule
                $schedule->markAsPaid($payment->id);
                $remainingPayment -= $scheduleAmount;
            } else {
                // Partial payment - reduce schedule amount
                $schedule->update([
                    'amount' => $scheduleAmount - $remainingPayment,
                    'notes' => "Partial payment of " . number_format($remainingPayment, 2) . " applied on " . now()->format('Y-m-d'),
                ]);
                $remainingPayment = 0;
            }
        }
    }

    /**
     * Get schedule statistics for an invoice
     */
    public function getScheduleStatistics(Invoice $invoice): array
    {
        $schedules = PaymentSchedule::where('invoice_id', $invoice->id)->get();

        return [
            'total_schedules' => $schedules->count(),
            'paid_schedules' => $schedules->where('status', PaymentSchedule::STATUS_PAID)->count(),
            'pending_schedules' => $schedules->where('status', PaymentSchedule::STATUS_PENDING)->count(),
            'overdue_schedules' => $schedules->where('status', PaymentSchedule::STATUS_OVERDUE)->count(),
            'total_scheduled_amount' => $schedules->sum('amount'),
            'paid_amount' => $schedules->where('status', PaymentSchedule::STATUS_PAID)->sum('amount'),
            'pending_amount' => $schedules->whereIn('status', [PaymentSchedule::STATUS_PENDING, PaymentSchedule::STATUS_OVERDUE])->sum('amount'),
        ];
    }
}
