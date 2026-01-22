<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tutor;
use Illuminate\Auth\Access\HandlesAuthorization;

class TutorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the tutor can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list tutors');
    }

    /**
     * Determine whether the tutor can view the model.
     */
    public function view(User $user, Tutor $model): bool
    {
        return $user->hasPermissionTo('view tutors');
    }

    /**
     * Determine whether the tutor can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create tutors');
    }

    /**
     * Determine whether the tutor can update the model.
     */
    public function update(User $user, Tutor $model): bool
    {
        return $user->hasPermissionTo('update tutors');
    }

    /**
     * Determine whether the tutor can delete the model.
     */
    public function delete(User $user, Tutor $model): bool
    {
        return $user->hasPermissionTo('delete tutors');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete tutors');
    }

    /**
     * Determine whether the tutor can restore the model.
     */
    public function restore(User $user, Tutor $model): bool
    {
        return false;
    }

    /**
     * Determine whether the tutor can permanently delete the model.
     */
    public function forceDelete(User $user, Tutor $model): bool
    {
        return false;
    }
}
