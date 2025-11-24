<?php

namespace App\Policies;

use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CourseSectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver la lista de secciones
        // Aquí puedes agregar lógica más específica, como roles o permisos
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('teacher');
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseSection $section): bool
    {
        // Por defecto, cualquier usuario autenticado puede ver una sección
        // Podrías restringir a solo secciones activas o donde el usuario esté relacionado
        // Ejemplo: return $section->active || $user->hasRole('admin') || $user->teacher_id === $section->teacher_id;
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo administradores o coordinadores pueden crear secciones
        // Aquí puedes agregar lógica para verificar si el usuario tiene permisos
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('coordinator');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseSection $section): bool
    {
        // Solo administradores o coordinadores pueden actualizar secciones
        // El profesor asignado podría actualizar ciertos campos
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('coordinator') || $user->teacher_id === $section->teacher_id;
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseSection $section): bool
    {
        // Solo administradores pueden eliminar secciones
        // Importante: verificar que no tenga estudiantes inscritos
        // Ejemplo: return $user->hasRole('admin') && $section->students()->count() === 0;
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseSection $section): bool
    {
        // Solo administradores pueden restaurar secciones eliminadas
        // Ejemplo: return $user->hasRole('admin');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseSection $section): bool
    {
        // Solo super administradores pueden eliminar permanentemente
        // Ejemplo: return $user->hasRole('super-admin');
        return false; // Por defecto restringido - implementar roles después
    }

    /**
     * Determine whether the user can enroll students in the section.
     */
    public function enrollStudents(User $user, CourseSection $section): bool
    {
        // Administradores, coordinadores o el profesor asignado pueden inscribir estudiantes
        // Ejemplo: return $user->hasRole('admin') || $user->hasRole('coordinator') || $user->teacher_id === $section->teacher_id;
        return false; // Por defecto restringido - implementar roles después
    }
}
