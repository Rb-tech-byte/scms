<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use \App\Traits\BranchScoped;

    protected $fillable = [
        'invoice_id',
        'applicant_id',
        'course_id',
        'payment_status',
        'amount',
        'receipt',
        'user_id',
        'date',
        'employee_id',
        'receipt_number',
        'installment_type'
    ];

    /*Generate Receipt number Automatically using boot method*/
    /** ALSO there is trigger which do this job whe boot method fail or when create method not used */
    /**
     * 
        DELIMITER $$
        
        CREATE TRIGGER BeforeInsertPayment
        BEFORE INSERT ON payments
        FOR EACH ROW
        BEGIN
            DECLARE last_number INT DEFAULT 0;

            -- Only generate a receipt number if it is not provided
            IF NEW.receipt_number IS NULL OR NEW.receipt_number = '' THEN
                -- Extract the numeric part of the most recent receipt number and increment it
                SELECT COALESCE(MAX(CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED)), 0) INTO last_number FROM payments;
                SET NEW.receipt_number = CONCAT('OUT-', LPAD(last_number + 1, 4, '0'));
            END IF;
        END$$

        DELIMITER ;

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

                    $lastFiveReceipts = Payment::latest('id')->take(5)->pluck('receipt_number');
                    while (in_array($newReceiptNumber, $lastFiveReceipts->toArray())) {
                        $newReceiptNumber = sprintf('OUT-%04d', ++$lastNumber);
                    }
                }
                $payment->receipt_number = $newReceiptNumber;
            }
        });
    }

    protected $searchableFields = ['*'];

    protected $casts = [
        'payment_status' => 'boolean',
        'date' => 'datetime',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee(){
        return $this->belongsTo(User::class,'employee_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
