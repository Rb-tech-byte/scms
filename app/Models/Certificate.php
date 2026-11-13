<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tracks course completion certificates through pending, ready, and issued states.
 */
class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'applicant_id',
        'course_id',
        'branch_id',
        'serial_number',
        'status',
        'ready_at',
        'issued_at',
        'issued_by',
        'notified_at',
    ];

    protected $casts = [
        'ready_at' => 'datetime',
        'issued_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}

