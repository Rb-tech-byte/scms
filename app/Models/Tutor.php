<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutor extends Model
{
    use HasFactory;
    use Searchable;
    use \App\Traits\BranchScoped;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'branch_id',
        'user_id',
        'active',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }
}
