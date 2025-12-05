<?php

namespace App\Policies;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Http\Controllers\PermissionController;

class AlunoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return PermissionController::isAuthorized('aluno.index');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Aluno $aluno): bool
    {
        return PermissionController::isAuthorized('aluno.show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return PermissionController::isAuthorized('aluno.create');
        return strtolower($user->role?->name ?? '') !== 'professor' && PermissionController::isAuthorized('aluno.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Aluno $aluno): bool
    {
        //return PermissionController::isAuthorized('aluno.edit');
return strtolower($user->role?->name ?? '') !== 'professor' && PermissionController::isAuthorized('aluno.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Aluno $aluno): bool
    {
        return PermissionController::isAuthorized('aluno.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Aluno $aluno): bool
    {
        return PermissionController::isAuthorized('aluno.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Aluno $aluno): bool
    {
        return PermissionController::isAuthorized('aluno.delete');
    }
}
