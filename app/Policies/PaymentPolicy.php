<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the payment can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list payments');
    }

    /**
     * Determine whether the payment can view the model.
     */
    public function view(User $user, Payment $model): bool
    {
        return $user->hasPermissionTo('view payments');
    }

    /**
     * Determine whether the payment can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create payments');
    }

    /**
     * Determine whether the payment can update the model.
     */
    public function update(User $user, Payment $model): bool
    {
        return $user->hasPermissionTo('update payments');
    }

    /**
     * Determine whether the payment can delete the model.
     */
    public function delete(User $user, Payment $model): bool
    {
        return $user->hasPermissionTo('delete payments');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete payments');
    }

    /**
     * Determine whether the payment can restore the model.
     */
    public function restore(User $user, Payment $model): bool
    {
        return false;
    }

    /**
     * Determine whether the payment can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $model): bool
    {
        return false;
    }
}
