<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the setting can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list settings');
    }

    /**
     * Determine whether the setting can view the model.
     */
    public function view(User $user, Setting $model): bool
    {
        return $user->hasPermissionTo('view settings');
    }

    /**
     * Determine whether the setting can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create settings');
    }

    /**
     * Determine whether the setting can update the model.
     */
    public function update(User $user, Setting $model): bool
    {
        return $user->hasPermissionTo('update settings');
    }

    /**
     * Determine whether the setting can delete the model.
     */
    public function delete(User $user, Setting $model): bool
    {
        return $user->hasPermissionTo('delete settings');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete settings');
    }

    /**
     * Determine whether the setting can restore the model.
     */
    public function restore(User $user, Setting $model): bool
    {
        return false;
    }

    /**
     * Determine whether the setting can permanently delete the model.
     */
    public function forceDelete(User $user, Setting $model): bool
    {
        return false;
    }
}
