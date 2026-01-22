<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PaymentSchedule extends Model
{
    use HasFactory;
    use \App\Traits\BranchScoped;

    protected $fillable = [
        'invoice_id',
        'installment_number',
        'due_date',
        'amount',
        'status',
        'sent_at',
        'paid_at',
        'payment_id',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'installment_number' => 'integer',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the invoice that owns the payment schedule
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the payment that fulfilled this schedule
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope: Pending schedules
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Overdue schedules
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE)
            ->orWhere(function ($q) {
                $q->where('status', self::STATUS_PENDING)
                    ->whereDate('due_date', '<', now());
            });
    }

    /**
     * Scope: Due soon (within X days)
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays($days));
    }

    /**
     * Scope: Paid schedules
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope: For a specific invoice
     */
    public function scopeForInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /**
     * Check if schedule is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_PAID 
            && $this->status !== self::STATUS_CANCELLED
            && $this->due_date->isPast();
    }

    /**
     * Check if schedule is due soon
     */
    public function isDueSoon($days = 7): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->due_date->isFuture()
            && $this->due_date->diffInDays(now()) <= $days;
    }

    /**
     * Mark schedule as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'sent_at' => now(),
            'status' => self::STATUS_SENT,
        ]);
    }

    /**
     * Mark schedule as paid
     */
    public function markAsPaid($paymentId = null): void
    {
        $this->update([
            'paid_at' => now(),
            'status' => self::STATUS_PAID,
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Mark schedule as overdue
     */
    public function markAsOverdue(): void
    {
        if ($this->status !== self::STATUS_PAID && $this->status !== self::STATUS_CANCELLED) {
            $this->update(['status' => self::STATUS_OVERDUE]);
        }
    }

    /**
     * Cancel schedule
     */
    public function cancel($notes = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_SENT => 'info',
            self::STATUS_OVERDUE => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'light',
        };
    }

    /**
     * Get days until due
     */
    public function getDaysUntilDueAttribute(): int
    {
        return $this->due_date->diffInDays(now(), false);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Auto-update status based on due date
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($schedule) {
            // Auto-update status to overdue if past due date
            if ($schedule->isOverdue() && $schedule->status === self::STATUS_PENDING) {
                $schedule->status = self::STATUS_OVERDUE;
            }
        });
    }
}
