<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    /**
     * Handle the Student "creating" event.
     */
    public function creating(Student $student): void
    {
        // Generar código de matrícula automáticamente si no se proporcionó
        if (empty($student->enrollment_code)) {
            $student->enrollment_code = $this->generateEnrollmentCode();
        }
    }

    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        \Log::info("Estudiante creado: {$student->enrollment_code} - {$student->full_name}");
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        if ($student->wasChanged('grade_level')) {
            \Log::info("Grado actualizado para estudiante {$student->enrollment_code}: {$student->grade_level}");
        }

        if ($student->wasChanged('active')) {
            $status = $student->active ? 'activado' : 'desactivado';
            \Log::info("Estudiante {$student->enrollment_code} {$status}");
        }
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        \Log::info("Estudiante eliminado: {$student->enrollment_code} - {$student->full_name}");
    }

    /**
     * Handle the Student "restored" event.
     */
    public function restored(Student $student): void
    {
        \Log::info("Estudiante restaurado: {$student->enrollment_code}");
    }

    /**
     * Handle the Student "force deleted" event.
     */
    public function forceDeleted(Student $student): void
    {
        \Log::warning("Estudiante eliminado permanentemente: {$student->enrollment_code}");
    }

    /**
     * Generate a unique enrollment code.
     */
    private function generateEnrollmentCode(): string
    {
        do {
            // Generar código en formato EST + año + 5 dígitos
            $year = date('Y');
            $code = 'EST' . $year . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Student::where('enrollment_code', $code)->exists());

        return $code;
    }
}
