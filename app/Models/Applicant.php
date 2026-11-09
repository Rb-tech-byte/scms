<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Applicant
 *
 * Represents candidate students registered into the Short Course Management System.
 *
 * @package App\Models
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 *
 * INTAKE LIFECYCLE & DEVELOPER GUIDE:
 * 1. Registration Flow:
 *    - Handled via `ApplicantController::store()`.
 *    - Creates a linked `User` record (with role 'student' and standardized phone format).
 *    - An `Enrollment` is instantiated for the chosen course and venue.
 *    - An `Invoice` is created for tuition accounting with optional staff/student discounts.
 *
 * 2. Multi-Branch Scope:
 *    - Uses `BranchScoped` trait.
 *    - Branch administrators and tutors can only see applicants enrolled within their assigned branch.
 *    - System administrators have global cross-branch visibility.
 *
 * 3. Discount & Affiliation Flags:
 *    - `is_staff`: Applicant is an employee of the university.
 *    - `is_staff_student` / `is_staff_family_member`: Child/dependent of a staff member (requires proof upload).
 *    - `is_out_student`: Student currently enrolled in mainstream Open University degree programs.
 *
 * 4. Audit Trail:
 *    - Changes to critical student profile attributes are automatically logged into `activity_log` table
 *      via Spatie Activitylog trait `getActivitylogOptions()`.
 */
class Applicant extends Model
{
    use LogsActivity;
    use HasFactory;
    use Searchable;
    use \App\Traits\BranchScoped;

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
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

    /**
     * Searchable fields utilized by the Searchable scope.
     *
     * @var array<string>
     */
    protected $searchableFields = ['*'];

    /**
     * Configure activity logging attributes for compliance auditing.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['full_name', 'phone', 'email', 'created_at', 'updated_at', 'is_staff_family_member', 'is_staff', 'is_out_student']);
    }

    /**
     * Country of nationality or residence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Authentication user account linked to this student applicant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tuition payments credited to this applicant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Course enrollment records for this applicant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Invoices issued to this applicant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * University branch/study centre where the applicant is registered.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
