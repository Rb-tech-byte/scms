<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Access\HandlesAuthorization;

class VenuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the venue can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list venues');
    }

    /**
     * Determine whether the venue can view the model.
     */
    public function view(User $user, Venue $model): bool
    {
        return $user->hasPermissionTo('view venues');
    }

    /**
     * Determine whether the venue can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create venues');
    }

    /**
     * Determine whether the venue can update the model.
     */
    public function update(User $user, Venue $model): bool
    {
        return $user->hasPermissionTo('update venues');
    }

    /**
     * Determine whether the venue can delete the model.
     */
    public function delete(User $user, Venue $model): bool
    {
        return $user->hasPermissionTo('delete venues');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete venues');
    }

    /**
     * Determine whether the venue can restore the model.
     */
    public function restore(User $user, Venue $model): bool
    {
        return false;
    }

    /**
     * Determine whether the venue can permanently delete the model.
     */
    public function forceDelete(User $user, Venue $model): bool
    {
        return false;
    }
}
