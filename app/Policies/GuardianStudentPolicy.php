<?php

namespace App\Policies;

use App\Models\GuardianStudent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GuardianStudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por ahora permitimos a todos los usuarios autenticados ver las relaciones
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GuardianStudent $guardianStudent): bool
    {
        // Los usuarios autenticados pueden ver las relaciones
        // En el futuro podríamos restringir según roles
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Por ahora permitimos a todos los usuarios autenticados crear relaciones
        // En el futuro podríamos restringir según roles (admin, teachers)
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GuardianStudent $guardianStudent): bool
    {
        // Por ahora permitimos a todos los usuarios autenticados actualizar relaciones
        // En el futuro podríamos restringir según roles
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GuardianStudent $guardianStudent): bool
    {
        // Por ahora permitimos a todos los usuarios autenticados eliminar relaciones
        // En el futuro podríamos restringir según roles
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GuardianStudent $guardianStudent): bool
    {

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GuardianStudent $guardianStudent): bool
    {
        return false;
    }
}
