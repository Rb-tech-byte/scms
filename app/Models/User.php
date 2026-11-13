<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
        'address',
        'password',
        'user_type',
        'davinci_role_id',
        'branch_id'
    ];

    protected $searchableFields = ['*'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }

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


    public function role(){
        return Role::find($this->davinci_role_id);
    }

    public function role_(){
        return $this->belongsTo(Role::class,'davinci_role_id','id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tutors()
    {
        return $this->hasMany(Tutor::class);
    }
    public function tutor()
    {
        return $this->hasOne(Tutor::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'davinci_user_permissions')->where('granted', 'yes');
    }

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
    public function assignRole($role)
    {
        if ($role instanceof Role) {
            $this->davinci_role_id = $role->id;
        } elseif (is_string($role)) {
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
