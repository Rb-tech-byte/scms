<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'location'];

    protected $searchableFields = ['*'];

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function tutors()
    {
        return $this->hasMany(Tutor::class);
    }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }
}
