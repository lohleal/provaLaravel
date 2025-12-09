<?php

namespace App\Policies;

use App\Models\Produto;
use App\Models\User;
use App\Http\Controllers\PermissionController;

class ProdutoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return PermissionController::isAuthorized('produto.index');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Produto $produto): bool
    {
        return PermissionController::isAuthorized('produto.show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return strtolower($user->role?->name ?? '') !== 'professor'
            && PermissionController::isAuthorized('produto.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Produto $produto): bool
    {
        return strtolower($user->role?->name ?? '') !== 'professor'
            && PermissionController::isAuthorized('produto.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Produto $produto): bool
    {
        return PermissionController::isAuthorized('produto.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Produto $produto): bool
    {
        return PermissionController::isAuthorized('produto.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Produto $produto): bool
    {
        return PermissionController::isAuthorized('produto.delete');
    }
}
