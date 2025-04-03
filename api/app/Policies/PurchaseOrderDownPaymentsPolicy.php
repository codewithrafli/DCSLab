<?php

namespace App\Policies;

use App\Enums\UserRoles;
use App\Models\PurchaseOrderDownPayments;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderDownPaymentsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->roles->isEmpty()) {
            return false;
        }

        if (! app()->environment('production') && $user->hasRole(UserRoles::DEVELOPER->value)) {
            return true;
        }

        if ($user->hasPermission('purchaseOrderDownPayments-readAny')) {
            return true;
        }
    }

    public function view(User $user, ?PurchaseOrderDownPayments $purchaseOrderDownPayments = null)
    {
        if ($user->roles->isEmpty()) {
            return false;
        }

        if (! app()->environment('production') && $user->hasRole(UserRoles::DEVELOPER->value)) {
            return true;
        }

        if ($user->hasPermission('purchaseOrderDownPayments-read')) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->roles->isEmpty()) {
            return false;
        }

        if (! app()->environment('production') && $user->hasRole(UserRoles::DEVELOPER->value)) {
            return true;
        }

        if ($user->hasPermission('purchaseOrderDownPayments-create')) {
            return true;
        }
    }

    public function update(User $user, ?PurchaseOrderDownPayments $purchaseOrderDownPayments = null)
    {
        if ($user->roles->isEmpty()) {
            return false;
        }

        if (! app()->environment('production') && $user->hasRole(UserRoles::DEVELOPER->value)) {
            return true;
        }

        if ($user->hasPermission('purchaseOrderDownPayments-update')) {
            return true;
        }
    }

    public function delete(User $user, ?PurchaseOrderDownPayments $purchaseOrderDownPayments = null)
    {
        if ($user->roles->isEmpty()) {
            return false;
        }

        if (! app()->environment('production') && $user->hasRole(UserRoles::DEVELOPER->value)) {
            return true;
        }

        if ($user->hasPermission('purchaseOrderDownPayments-delete')) {
            return true;
        }
    }

    public function restore(User $user, PurchaseOrderDownPayments $purchaseOrderDownPayments)
    {
        return false;
    }

    public function forceDelete(User $user, PurchaseOrderDownPayments $purchaseOrderDownPayments)
    {
        return false;
    }
}
