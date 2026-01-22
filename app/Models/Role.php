<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table = 'davinci_roles';

    protected $fillable = ['name'];

    protected $searchableFields = ['*'];

    /**
     * Get users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class, 'davinci_role_id');
    }

    /**
     * Get role permissions (pivot table)
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

    /**
     * Get permissions through the pivot table
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'davinci_role_permissions',
            'role_id',
            'permission_id'
        )->wherePivot('granted', 'yes');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }

        if ($permission instanceof Permission) {
            return $this->permissions()->where('davinci_permissions.id', $permission->id)->exists();
        }

        if (is_numeric($permission)) {
            return $this->permissions()->where('davinci_permissions.id', $permission)->exists();
        }

        return false;
    }

    /**
     * Sync permissions for this role
     */
    public function syncPermissions($permissions)
    {
        // First, set all existing permissions to 'no'
        RolePermission::where('role_id', $this->id)->update(['granted' => 'no']);

        // Then update or create permissions with 'yes'
        foreach ($permissions as $permission) {
            $permissionId = $permission instanceof Permission ? $permission->id : $permission;
            
            RolePermission::updateOrCreate(
                [
                    'role_id' => $this->id,
                    'permission_id' => $permissionId
                ],
                [
                    'granted' => 'yes'
                ]
            );
        }
    }

    /**
     * Give permission to role
     */
    public function givePermissionTo($permission)
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        
        RolePermission::updateOrCreate(
            [
                'role_id' => $this->id,
                'permission_id' => $permissionId
            ],
            [
                'granted' => 'yes'
            ]
        );
    }

    /**
     * Revoke permission from role
     */
    public function revokePermissionTo($permission)
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        
        RolePermission::where('role_id', $this->id)
            ->where('permission_id', $permissionId)
            ->update(['granted' => 'no']);
    }

    /**
     * Find role by name
     */
    public static function findByName($name)
    {
        return static::where('name', $name)->first();
    }
}
