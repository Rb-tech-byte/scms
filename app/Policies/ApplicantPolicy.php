<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Applicant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the applicant can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list applicants');
    }

    /**
     * Determine whether the applicant can view the model.
     */
    public function view(User $user, Applicant $model): bool
    {
        return $user->hasPermissionTo('view applicants');
    }

    /**
     * Determine whether the applicant can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create applicants');
    }

    /**
     * Determine whether the applicant can update the model.
     */
    public function update(User $user, Applicant $model): bool
    {
        return $user->hasPermissionTo('update applicants');
    }

    /**
     * Determine whether the applicant can delete the model.
     */
    public function delete(User $user, Applicant $model): bool
    {
        return $user->hasPermissionTo('delete applicants');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete applicants');
    }

    /**
     * Determine whether the applicant can restore the model.
     */
    public function restore(User $user, Applicant $model): bool
    {
        return false;
    }

    /**
     * Determine whether the applicant can permanently delete the model.
     */
    public function forceDelete(User $user, Applicant $model): bool
    {
        return false;
    }
}
