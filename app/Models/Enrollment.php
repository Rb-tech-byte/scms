<?php
namespace App\Models;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Enrollment extends Model
{
    use HasFactory;
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
}
