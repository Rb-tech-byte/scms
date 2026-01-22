<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the notification can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list notifications');
    }

    /**
     * Determine whether the notification can view the model.
     */
    public function view(User $user, Notification $model): bool
    {
        return $user->hasPermissionTo('view notifications');
    }

    /**
     * Determine whether the notification can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create notifications');
    }

    /**
     * Determine whether the notification can update the model.
     */
    public function update(User $user, Notification $model): bool
    {
        return $user->hasPermissionTo('update notifications');
    }

    /**
     * Determine whether the notification can delete the model.
     */
    public function delete(User $user, Notification $model): bool
    {
        return $user->hasPermissionTo('delete notifications');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete notifications');
    }

    /**
     * Determine whether the notification can restore the model.
     */
    public function restore(User $user, Notification $model): bool
    {
        return false;
    }

    /**
     * Determine whether the notification can permanently delete the model.
     */
    public function forceDelete(User $user, Notification $model): bool
    {
        return false;
    }
}
