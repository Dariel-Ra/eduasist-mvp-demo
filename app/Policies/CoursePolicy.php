<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver la lista de cursos
        // Aquí puedes agregar lógica más específica, como roles o permisos
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('teacher');
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver un curso
        // Podrías restringir a solo cursos activos o cursos donde el usuario esté relacionado
        // Ejemplo: return $course->active || $user->hasRole('admin');
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo administradores pueden crear cursos
        // Aquí puedes agregar lógica para verificar si el usuario tiene permisos
        // Ejemplo: return $user->hasRole('admin') || $user->hasPermission('create-courses');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // Solo administradores pueden actualizar cursos
        // O coordinadores académicos
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('coordinator');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        // Solo administradores pueden eliminar cursos
        // Importante: verificar que no tenga secciones activas o estudiantes inscritos
        // Ejemplo: return $user->hasRole('admin') && $course->sections()->count() === 0;
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        // Solo administradores pueden restaurar cursos eliminados
        // Ejemplo: return $user->hasRole('admin');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        // Solo super administradores pueden eliminar permanentemente
        // Ejemplo: return $user->hasRole('super-admin');
        return false; // Por defecto restringido - implementar roles después
    }
}
