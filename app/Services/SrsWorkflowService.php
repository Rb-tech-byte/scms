<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Shared business workflows for control numbers, receipt uploads, and certificate eligibility.
 */
class SrsWorkflowService
{
    /**
     * Generate and persist a unique institutional control number on an invoice.
     */
    public static function ensureControlNumber(Invoice $invoice): string
    {
        if (!empty($invoice->control_number)) {
            return $invoice->control_number;
        }

        do {
            $candidate = '99104' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (Invoice::where('control_number', $candidate)->exists());

        $invoice->forceFill([
            'control_number' => $candidate,
            'control_number_generated_at' => now(),
        ])->save();

        return $candidate;
    }

    /**
     * Record a tuition payment against an invoice and recalculate invoice balance and status atomically.
     *
     * @param array<string, mixed> $attributes
     */
    public static function applyPaymentToInvoice(Invoice $invoice, array $attributes): Payment
    {
        return DB::transaction(function () use ($invoice, $attributes): Payment {
            $lockedInvoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $amount = (float) $attributes['amount'];
            $newBalance = max(0.0, (float) $lockedInvoice->balance - $amount);
            $newPaid = (float) $lockedInvoice->paid_amount + $amount;
            $status = $newBalance <= 0.009 ? '1' : ($newPaid > 0 ? '2' : '0');

            $lockedInvoice->update([
                'balance' => $newBalance,
                'paid_amount' => $newPaid,
                'status' => $status,
                'payment_method' => $attributes['payment_method'] ?? $lockedInvoice->payment_method,
            ]);

            return Payment::create([
                'invoice_id' => $lockedInvoice->id,
                'applicant_id' => $lockedInvoice->applicant_id,
                'course_id' => $lockedInvoice->enrollment->course_id,
                'payment_status' => $newBalance <= 0.009 ? 0 : 1,
                'amount' => $amount,
                'payment_method' => $attributes['payment_method'] ?? 'control_number',
                'transaction_reference' => $attributes['transaction_reference'] ?? null,
                'receipt' => $attributes['receipt'] ?? null,
                'receipt_file' => $attributes['receipt_file'] ?? null,
                'verification_status' => $attributes['verification_status'] ?? 'verified',
                'user_id' => $attributes['user_id'],
                'employee_id' => $attributes['employee_id'] ?? null,
                'date' => $attributes['date'] ?? now(),
                'installment_type' => $attributes['installment_type'] ?? ($newBalance <= 0.009 ? 'full' : 'half'),
            ]);
        });
    }

    /**
     * Find completed and fully-paid enrollments eligible for completion certificates.
     */
    public static function getEligibleEnrollments(?int $branchId = null): Collection
    {
        return Enrollment::with(['applicant', 'course', 'venue.branch'])
            ->whereHas('invoices', function ($q) {
                $q->where('balance', '<=', 0)->where('status', '1');
            })
            ->whereDate('end_date', '<=', Carbon::today())
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('venue', fn ($vq) => $vq->where('branch_id', $branchId));
            })
            ->latest('end_date')
            ->get();
    }

    /**
     * Create or return a Certificate record for a given enrollment.
     */
    public static function ensureCertificate(Enrollment $enrollment, string $status = 'ready'): Certificate
    {
        $existing = Certificate::where('enrollment_id', $enrollment->id)->first();
        if ($existing) {
            if ($status === 'ready' && $existing->status === 'pending') {
                $existing->update(['status' => 'ready', 'ready_at' => now()]);
            }
            return $existing;
        }

        $branchId = $enrollment->venue?->branch_id ?? $enrollment->applicant?->branch_id ?? 1;
        $serial = 'OUT-CERT-' . date('Y') . '-' . strtoupper(Str::random(6));

        return Certificate::create([
            'enrollment_id' => $enrollment->id,
            'applicant_id' => $enrollment->applicant_id,
            'course_id' => $enrollment->course_id,
            'branch_id' => $branchId,
            'serial_number' => $serial,
            'status' => $status,
            'ready_at' => $status !== 'pending' ? now() : null,
        ]);
    }
}

