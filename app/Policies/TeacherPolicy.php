<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    /**
     * Determine whether the user can view any teachers.
     */
    public function viewAny(User $user): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver la lista de profesores
        // Aquí puedes agregar lógica más específica, como roles o permisos
        return true;
    }

    /**
     * Determine whether the user can view the teacher.
     */
    public function view(User $user, Teacher $teacher): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver un profesor
        return true;
    }

    /**
     * Determine whether the user can create teachers.
     */
    public function create(User $user): bool
    {
        // Aquí puedes agregar lógica para verificar si el usuario tiene permisos
        // Por ejemplo: return $user->hasRole('admin');
        return true;
    }

    /**
     * Determine whether the user can update the teacher.
     */
    public function update(User $user, Teacher $teacher): bool
    {
        // Un profesor puede actualizar su propio perfil
        // O un administrador puede actualizar cualquier perfil
        // Por ejemplo: return $user->id === $teacher->user_id || $user->hasRole('admin');
        return $user->id === $teacher->user_id;
    }

    /**
     * Determine whether the user can delete the teacher.
     */
    public function delete(User $user, Teacher $teacher): bool
    {
        // Solo administradores pueden eliminar profesores
        // Por ejemplo: return $user->hasRole('admin');
        return false; // Por defecto, nadie puede eliminar
    }

    /**
     * Determine whether the user can restore the teacher.
     */
    public function restore(User $user, Teacher $teacher): bool
    {
        // Solo administradores pueden restaurar profesores eliminados
        return false;
    }

    /**
     * Determine whether the user can permanently delete the teacher.
     */
    public function forceDelete(User $user, Teacher $teacher): bool
    {
        // Solo administradores pueden eliminar permanentemente
        return false;
    }
}
