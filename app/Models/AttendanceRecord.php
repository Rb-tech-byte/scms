<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tracks per-session student attendance linked to a course assignment and enrollment.
 */
class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_assignment_id',
        'enrollment_id',
        'applicant_id',
        'tutor_id',
        'status',
        'marked_at',
        'notes',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function courseAssignment()
    {
        return $this->belongsTo(CourseAssignment::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}

