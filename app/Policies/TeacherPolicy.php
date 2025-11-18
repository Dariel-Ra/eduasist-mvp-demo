<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver la lista de profesores
        // Aquí puedes agregar lógica más específica, como roles o permisos
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Teacher $teacher): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver un profesor
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Aquí puedes agregar lógica para verificar si el usuario tiene permisos
        // Por ejemplo: return $user->hasRole('admin');
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Teacher $teacher): bool
    {
        // Un profesor puede actualizar su propio perfil
        // O un administrador puede actualizar cualquier perfil
        // Por ejemplo: return $user->id === $teacher->user_id || $user->hasRole('admin');
        return $user->id === $teacher->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Teacher $teacher): bool
    {
        // Solo administradores pueden eliminar profesores
        // Por ejemplo: return $user->hasRole('admin');
        return false; // Por defecto, nadie puede eliminar
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Teacher $teacher): bool
    {
        // Solo administradores pueden restaurar profesores eliminados
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Teacher $teacher): bool
    {
        return false;
    }
}
