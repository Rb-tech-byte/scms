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
        'davinci_role_id',
        'branch_id',
        'force_password_reset',
        'is_blocked',
        'blocked_at',
        'blocked_reason',
        'failed_login_attempts',
        'lockout_count',
        'locked_until',
        'password_changed_at',
        'active_session_id',
        'last_activity_at',
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
        'force_password_reset' => 'boolean',
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Check whether the user's password has exceeded the 3-month SRS expiry policy.
     */
    public function isPasswordExpired(): bool
    {
        if (!$this->password_changed_at) {
            return false;
        }

        return $this->password_changed_at->lt(now()->subMonths(3));
    }

    /**
     * Check whether the user is currently in a temporary login lockout window.
     */
    public function isLockedOut(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

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
     * Get the assigned branch/center for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
            $userRole = $this->role()?->name;

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
        if (!$permission) {
            return false;
        }

        if ($this->hasRole('system-admin')) {
            return true;
        }

        $raw = trim((string) $permission);
        $normalized = strtolower($raw);
        $normalized = preg_replace('/\s+/', '-', $normalized);
        $normalized = str_replace('_', '-', $normalized);

        $permissions = $this->relationLoaded('permissions')
            ? $this->permissions
            : $this->permissions()->get();

        if ($permissions->contains('name', $raw) || $permissions->contains('name', $normalized)) {
            return true;
        }

        $role = $this->role();
        if ($role) {
            return $role->hasPermissionTo($raw) || $role->hasPermissionTo($normalized);
        }

        return false;
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

    /**
     * Get user's branch (Accessor)
     * Usage: $user->branch
     */
    public function getBranchAttribute()
    {
        // 1. Check if user is a tutor
        // Use the relationship 'tutor' (singular)
        if ($this->relationLoaded('tutor')) {
            $tutor = $this->tutor;
        } else {
            $tutor = $this->tutor()->first();
        }

        if ($tutor) {
            // Assuming Tutor belongsTo Branch
            return $tutor->branch; 
        }

        // 2. Check if user is an applicant
        // Use 'applicants' (hasMany) - usually one active applicant?
        if ($this->relationLoaded('applicants')) {
            $applicant = $this->applicants->first();
        } else {
            $applicant = $this->applicants()->first();
        }

        if ($applicant) {
            return $applicant->branch;
        }
        
        // 3. Check direct branch_id if exists
        if (isset($this->branch_id)) {
            return \App\Models\Branch::find($this->branch_id);
        }

        return null;
    }
}
