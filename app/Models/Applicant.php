<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Applicant extends Model
{
    use LogsActivity;
    use HasFactory;
    use Searchable;
    use \App\Traits\BranchScoped;

    protected $fillable = [
        'full_name',
        'home_address',
        'country_id',
        'office_of_work',
        'occupation',
        'mobile',
        'email',
        'name_of_sponsor',
        'phone_2',
        'education_level',
        'user_id',
        'is_staff_family_member',
        'is_staff',
        'is_out_student',
        'branch_id',
        'is_staff_student',
        'parent_staff_id'
    ];

    protected $searchableFields = ['*'];

    public function getActivitylogOptions(): LogOptions
    {

        return LogOptions::defaults()
            ->logOnly(['full_name', 'phone', 'email','created_at','updated_at','is_staff_family_member','is_staff','is_out_student']);

    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
