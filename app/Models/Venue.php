<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;
    use Searchable;
    use \App\Traits\BranchScoped;

    protected $fillable = ['name', 'branch_id'];

    protected $searchableFields = ['*'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }
}
