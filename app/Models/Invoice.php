<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Invoice
 *
 * Core student tuition billing and accounting ledger model.
 *
 * BILLING LIFECYCLE & DEVELOPER GUIDE:
 * 1. Invoice Generation:
 *    - Automatically instantiated upon applicant enrollment confirmation in ApplicantController.
 *    - `invoice_prefix`: Unique invoice number formatted by `generateInvoicePrefix()` in `app/Helpers/Helpers.php`.
 *    - Initial balance equals `Course::fee` minus `discount_amount`.
 *
 * 2. Status Transitions (`status` column):
 *    - '0' (Unpaid): No payments logged; full balance outstanding.
 *    - '1' (Paid): Balance is zero ($0.00 / 0 Tsh). Student is cleared for completion certificate.
 *    - '2' (Partially Paid): One or more partial installment payments made; remaining balance > 0.
 *
 * 3. Discounts:
 *    - When `has_discount` is true, `discount_amount` reduces the net receivable balance.
 *    - `discount_reason` records institutional justification (e.g. staff child waiver, student discount).
 *    - `discount_file` stores the audit proof attachment path.
 *
 * 4. Multi-Installment Payment Schedules:
 *    - Linked to `PaymentSchedule` entries tracking upcoming installment milestones and due dates.
 *    - The `balance` and `paid_amount` are updated atomically in `PaymentController::store()`.
 */
class Invoice extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use \App\Traits\BranchScoped;

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'enrollment_id',
        'balance',
        'paid_amount',
        'description',
        'invoice_prefix',
        'has_discount',
        'discount_amount',
        'discount_reason',
        'discount_file',
        'date',
        'status',
    ];

    /**
     * Searchable fields utilized by the Searchable scope.
     *
     * @var array<string>
     */
    protected $searchableFields = ['*'];

    /**
     * Attribute type casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Student applicant to whom this invoice is billed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Academic enrollment instance linking the course, session, and venue.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * All payments credited against this invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scheduled installment milestones defined for this invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }
}
