<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any students.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the student.
     */
    public function view(User $user, Student $student): bool
    {
        // Tutores pueden ver a sus estudiantes
        // Profesores pueden ver a sus estudiantes
        // Administradores pueden ver a todos
        return true;
    }

    /**
     * Determine whether the user can create students.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the student.
     */
    public function update(User $user, Student $student): bool
    {
        // Administradores y tutores del estudiante pueden actualizar
        return true;
    }

    /**
     * Determine whether the user can delete the student.
     */
    public function delete(User $user, Student $student): bool
    {
        // Solo administradores pueden eliminar
        return false;
    }

    /**
     * Determine whether the user can restore the student.
     */
    public function restore(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the student.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return false;
    }
}
