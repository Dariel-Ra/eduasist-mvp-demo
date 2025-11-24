<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseObserver
{
    /**
     * Handle the Course "creating" event.
     */
    public function creating(Course $course): void
    {
        // Generar código automáticamente si no se proporcionó
        if (empty($course->code)) {
            $course->code = $this->generateUniqueCode();
        }

        // Normalizar el código a mayúsculas
        if ($course->code) {
            $course->code = strtoupper($course->code);
        }
    }

    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        // Logging de creación de curso
        \Log::info("Curso creado: {$course->code} - {$course->name}");

        // Aquí puedes agregar lógica adicional:
        // - Notificar a administradores
        // - Crear registros relacionados por defecto
        // - Limpiar cache de cursos
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {
        // Logging de cambios importantes
        if ($course->wasChanged('active')) {
            $status = $course->active ? 'activado' : 'desactivado';
            \Log::info("Curso {$course->code} ha sido {$status}");
        }

        if ($course->wasChanged('name')) {
            \Log::info("Nombre del curso {$course->code} actualizado: {$course->name}");
        }

        // Aquí puedes agregar lógica adicional:
        // - Limpiar cache de cursos
        // - Notificar a profesores asignados
        // - Actualizar índices de búsqueda
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        // Logging de eliminación
        \Log::info("Curso eliminado: {$course->code} - {$course->name}");

        // Aquí puedes agregar lógica adicional:
        // - Limpiar datos relacionados (secciones, inscripciones)
        // - Notificar a administradores
        // - Archivar información importante
    }

    /**
     * Handle the Course "restored" event.
     */
    public function restored(Course $course): void
    {
        // Logging de restauración
        \Log::info("Curso restaurado: {$course->code} - {$course->name}");
    }

    /**
     * Handle the Course "force deleted" event.
     */
    public function forceDeleted(Course $course): void
    {
        // Logging de eliminación permanente
        \Log::warning("Curso eliminado permanentemente: {$course->code} - {$course->name}");

        // Aquí puedes agregar lógica adicional:
        // - Eliminar archivos relacionados
        // - Limpiar completamente de cache
    }

    /**
     * Generate a unique course code.
     */
    private function generateUniqueCode(): string
    {
        do {
            // Generar código en formato CRS + 4 dígitos
            $code = 'CRS-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Course::where('code', $code)->exists());

        return $code;
    }
}
