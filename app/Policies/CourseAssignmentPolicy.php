<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseAssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the courseAssignment can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list courseassignments');
    }

    /**
     * Determine whether the courseAssignment can view the model.
     */
    public function view(User $user, CourseAssignment $model): bool
    {
        return $user->hasPermissionTo('view courseassignments');
    }

    /**
     * Determine whether the courseAssignment can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create courseassignments');
    }

    /**
     * Determine whether the courseAssignment can update the model.
     */
    public function update(User $user, CourseAssignment $model): bool
    {
        return $user->hasPermissionTo('update courseassignments');
    }

    /**
     * Determine whether the courseAssignment can delete the model.
     */
    public function delete(User $user, CourseAssignment $model): bool
    {
        return $user->hasPermissionTo('delete courseassignments');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete courseassignments');
    }

    /**
     * Determine whether the courseAssignment can restore the model.
     */
    public function restore(User $user, CourseAssignment $model): bool
    {
        return false;
    }

    /**
     * Determine whether the courseAssignment can permanently delete the model.
     */
    public function forceDelete(User $user, CourseAssignment $model): bool
    {
        return false;
    }
}
