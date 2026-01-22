<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseAssignment extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'branch_id',
        'venue_id',
        'course_id',
        'date',
        'start_time',
        'end_time',
        'tutor_id',
        'active',
        'is_teached'
    ];

    protected $searchableFields = ['*'];

    protected $table = 'course_assignments';

    protected $casts = [
        'date' => 'date',
        'active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
