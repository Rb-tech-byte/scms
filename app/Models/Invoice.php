<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use \App\Traits\BranchScoped;

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
    

    protected $searchableFields = ['*'];

    protected $casts = [
        'date' => 'date',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }
}
