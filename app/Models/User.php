<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * Core authentication, profile, and authorization entity.
 *
 * @package App\Models
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 *
 * DEVELOPER ARCHITECTURE GUIDE:
 * 1. Authentication & Security:
 *    - Uses standard Laravel guard authentication.
 *    - Passwords must comply with entropy standards (min 8 chars, mixed case, numbers, symbols).
 *    - `force_password_reset` attribute triggers the ForcePasswordReset middleware when true.
 *
 * 2. Role-Based Access Control (RBAC):
 *    - Role is referenced via `davinci_role_id` linking to `App\Models\Role`.
 *    - Role names convention: 'system-admin', 'branch-admin', 'tutor', 'student'.
 *    - Granular permissions are attached through the pivot table `davinci_user_permissions`.
 *
 * 3. User Types (`user_type` column):
 *    - 'staff': Administrative employees, registrars, tutors, branch directors.
 *    - 'student': Enrolled course participants.
 *
 * 4. Extension Guidelines:
 *    - To add multi-factor authentication (MFA/2FA), add `two_factor_secret` and `two_factor_confirmed_at`.
 *    - To support external identity providers (Google / Microsoft Azure AD SSO), map `oauth_provider` and `oauth_id`.
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
        'address',
        'password',
        'user_type',
        'davinci_role_id'
    ];

    /**
     * Searchable fields utilized by the Searchable scope.
     *
     * @var array<string>
     */
    protected $searchableFields = ['*'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Attribute cast definitions.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all applicant intake records linked to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * Retrieve the branch name associated with this user's student applicant record.
     *
     * @return string|null
     */
    public function applicant_branch()
    {
        $applicant =  Applicant::where('user_id', $this->id)->first();
        if($applicant){
            $branch = Branch::where('id', $applicant->branch_id)->first();
            if($branch){
                return $branch->name;
            }
        }
    }

    /**
     * Determine whether the user possesses a given role or any of the supplied roles.
     *
     * @param string|array $role Role name or array of acceptable role names (e.g. 'system-admin', ['branch-admin', 'tutor'])
     * @return bool
     */
    public function hasRole($role)
    {
        if (!empty($this->davinci_role_id)) {
            $userRole = $this->role()->name;

            if (is_array($role)) {
                foreach ($role as $r) {
                    if ($userRole == $r) {
                        return true;
                    }
                }
            } else {
                if ($userRole == $role) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Direct query to find the Role model associated with this user.
     *
     * @return \App\Models\Role|null
     */
    public function role()
    {
        return Role::find($this->davinci_role_id);
    }

    /**
     * Belongs-to relationship for the user's assigned role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role_()
    {
        return $this->belongsTo(Role::class,'davinci_role_id','id');
    }

    /**
     * Payments credited to or recorded by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Tutor profiles linked to this user (for academic instructors).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tutors()
    {
        return $this->hasMany(Tutor::class);
    }

    /**
     * Single tutor profile for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tutor()
    {
        return $this->hasOne(Tutor::class);
    }

    /**
     * Granular permissions granted directly to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'davinci_user_permissions')->where('granted', 'yes');
    }

    /**
     * Verify whether the user possesses a granted permission.
     *
     * @param string $permission The permission identifier name (e.g. 'List applicants')
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission);
    }

    /**
     * Assign a new role to the user and persist the change.
     *
     * @param \App\Models\Role|string|int $role
     * @return bool
     * @throws \Exception
     */
    public function assignRole($role)
    {
        if ($role instanceof Role) {
            $this->davinci_role_id = $role->id;
        } elseif (is_string($role) || is_int($role)) {
            $this->davinci_role_id = $role;
        } else {
            throw new \Exception("Invalid role format.");
        }
        
        return $this->save();
    }
}
