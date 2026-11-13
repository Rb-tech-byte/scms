<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Payment
 *
 * Handles tuition payments, installment settlements, and official receipt numbering.
 *
 * @package App\Models
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 *
 * FINANCIAL AUDIT & DEVELOPER GUIDE:
 * 1. Financial Immutability & Audit Trail:
 *    - Payments represent legally compliant financial records.
 *    - Once created, payments should NOT be freely edited or deleted.
 *    - SoftDeletes is enabled to maintain historical ledger integrity even if revoked.
 *    - Unauthorized edit attempts are trapped and logged via Spatie Activitylog in PaymentController.
 *
 * 2. Automated Receipt Number Generation:
 *    - Format: 'OUT-XXXX' (e.g. OUT-0001, OUT-0002).
 *    - Handled primarily by the `boot()` creating event hook with collision avoidance.
 *    - Database trigger `BeforeInsertPayment` serves as a failsafe if records are inserted via raw SQL.
 *
 * 3. Installment Types & Status:
 *    - installment_type: 'quarter' (25%), 'half' (50%), 'full' (100% of outstanding balance).
 *    - payment_status: 0 = Full Payment, 1 = Partial Payment.
 *
 * 4. Automated Payment Gateway Integration (M-Pesa / GePG / AirtelMoney / TigoPesa):
 *    - Future developers can attach webhook/callback endpoints that populate this model.
 *    - When a webhook confirms receipt of funds:
 *      a) Populate `receipt_number`, `amount`, `date`, `applicant_id`, `invoice_id`.
 *      b) Update the parent `Invoice` (`balance` = `balance - amount`, `paid_amount` += amount).
 *      c) Trigger automated SMS confirmation via `app/Helpers/Helpers.php` notification helpers.
 */
class Payment extends Model
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
        'invoice_id',
        'applicant_id',
        'course_id',
        'payment_status',
        'amount',
        'payment_method',
        'transaction_reference',
        'receipt',
        'receipt_file',
        'verification_status',
        'user_id',
        'date',
        'employee_id',
        'receipt_number',
        'installment_type'
    ];

    /**
     * Boot the model to auto-generate unique sequential receipt numbers.
     *
     * DELIMITER $$
     * CREATE TRIGGER BeforeInsertPayment
     * BEFORE INSERT ON payments
     * FOR EACH ROW
     * BEGIN
     *     DECLARE last_number INT DEFAULT 0;
     *     IF NEW.receipt_number IS NULL OR NEW.receipt_number = '' THEN
     *         SELECT COALESCE(MAX(CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED)), 0) INTO last_number FROM payments;
     *         SET NEW.receipt_number = CONCAT('OUT-', LPAD(last_number + 1, 4, '0'));
     *     END IF;
     * END$$
     * DELIMITER ;
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!isset($payment->receipt_number) || empty($payment->receipt_number)) {
                $lastReceipt = Payment::latest('id')->first();
                $newReceiptNumber = 'OUT-0001';

                if ($lastReceipt && !empty($lastReceipt->receipt_number)) {
                    $lastNumber = intval(substr($lastReceipt->receipt_number, 4));
                    $newReceiptNumber = sprintf('OUT-%04d', $lastNumber + 1);

                    // Check recent receipts to prevent collisions under concurrent load
                    $lastFiveReceipts = Payment::latest('id')->take(5)->pluck('receipt_number');
                    while (in_array($newReceiptNumber, $lastFiveReceipts->toArray())) {
                        $newReceiptNumber = sprintf('OUT-%04d', ++$lastNumber);
                    }
                }
                $payment->receipt_number = $newReceiptNumber;
            }
        });
    }

    /**
     * Fields searchable by the Searchable trait.
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
        'payment_status' => 'boolean',
        'date' => 'datetime',
    ];

    /**
     * Student applicant who made the payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Course associated with this payment transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * User account of the student applicant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cashier, accountant, or staff member who logged/verified this payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id');
    }

    /**
     * Parent invoice against which this payment is applied.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
