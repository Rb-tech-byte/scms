<?php
namespace App\Models;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Enrollment extends Model
{
    use HasFactory;
    use \App\Traits\BranchScoped;
    use Searchable;
    protected $fillable = [
        'course_id',
        'venue_id',
        'session',
        'applicant_id',
        'start_date',
        'end_date',
        'fee',
        'duration',
        'enrollment_status',
        'attendance_percentage',
    ];
    protected $searchableFields = ['*'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
    public function invoices()
    {
        return $this->hasOne(Invoice::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
