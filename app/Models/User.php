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
        'davinci_role_id'
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

   /* public function hasRole($role){
        
        if(!empty($this->davinci_role_id)){
            $userRole = $this->role()->name;
        if($userRole==$role){
                return true;
            }
            return false;
        }
        return false;
    }*/

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
        return $this->permissions->contains('name', $permission);
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
}
