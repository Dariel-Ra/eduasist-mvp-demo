<?php

namespace App\Observers;

use App\Models\CourseSection;
use App\Enums\ScheduleDay;
use Illuminate\Support\Str;

class CourseSectionObserver
{
    /**
     * Handle the CourseSection "creating" event.
     */
    public function creating(CourseSection $section): void
    {
        // Aquí puedes agregar lógica antes de crear
        // Por ejemplo: validar conflictos de horario del profesor
    }

    /**
     * Handle the CourseSection "created" event.
     */
    public function created(CourseSection $section): void
    {
        // Logging de creación de sección
        \Log::info("Sección de curso creada: Curso {$section->course->name} - Sección {$section->section}");

        // Aquí puedes agregar lógica adicional:
        // - Notificar al profesor asignado
        // - Crear calendario de clases
        // - Actualizar estadísticas del curso
    }

    /**
     * Handle the CourseSection "updated" event.
     */
    public function updated(CourseSection $section): void
    {
        // Logging de cambios importantes
        if ($section->wasChanged('teacher_id')) {
            \Log::info("Profesor cambiado en sección {$section->id}: antiguo {$section->getOriginal('teacher_id')} -> nuevo {$section->teacher_id}");
        }

        if ($section->wasChanged('schedule_days')) {
            $oldDays = $this->formatDaysForLog($section->getOriginal('schedule_days'));
            $newDays = $this->formatDaysForLog($section->schedule_days);
            \Log::info("Días de clase actualizados en sección {$section->id}: {$oldDays} -> {$newDays}");
        }

        if ($section->wasChanged('start_time') || $section->wasChanged('end_time')) {
            $oldStart = $section->getOriginal('start_time');
            $oldEnd = $section->getOriginal('end_time');
            $newStart = $section->getRawOriginal('start_time'); // Obtener el valor raw
            $newEnd = $section->getRawOriginal('end_time');
            \Log::info("Horario actualizado en sección {$section->id}: {$oldStart}-{$oldEnd} -> {$newStart}-{$newEnd}");
        }

        if ($section->wasChanged('active')) {
            $status = $section->active ? 'activada' : 'desactivada';
            \Log::info("Sección {$section->id} ha sido {$status}");
        }

        // Aquí puedes agregar lógica adicional:
        // - Notificar a estudiantes si cambió el horario
        // - Notificar al nuevo profesor asignado
        // - Actualizar calendario de clases
    }

    /**
     * Format schedule days for logging with Spanish labels.
     *
     * @param string|array|null $days
     * @return string
     */
    private function formatDaysForLog($days): string
    {
        if (empty($days)) {
            return 'ninguno';
        }

        // Si es string (valor raw de la DB), convertir a array
        if (is_string($days)) {
            $days = explode(',', $days);
        }

        // Convertir valores a enums y obtener labels
        $labels = collect($days)
            ->map(fn($day) => ScheduleDay::tryFrom($day)?->label() ?? $day)
            ->join(', ');

        return $labels;
    }

    /**
     * Handle the CourseSection "deleting" event.
     */
    public function deleting(CourseSection $section): void
    {
        // Verificar si tiene estudiantes inscritos
        if ($section->students()->count() > 0) {
            \Log::warning("Intentando eliminar sección {$section->id} con estudiantes inscritos");
        }
    }

    /**
     * Handle the CourseSection "deleted" event.
     */
    public function deleted(CourseSection $section): void
    {
        // Logging de eliminación
        \Log::info("Sección de curso eliminada: ID {$section->id}");

        // Aquí puedes agregar lógica adicional:
        // - Limpiar asistencias relacionadas
        // - Notificar a profesores y estudiantes
        // - Archivar información importante
    }

    /**
     * Handle the CourseSection "restored" event.
     */
    public function restored(CourseSection $section): void
    {
        // Logging de restauración
        \Log::info("Sección de curso restaurada: ID {$section->id}");
    }

    /**
     * Handle the CourseSection "force deleted" event.
     */
    public function forceDeleted(CourseSection $section): void
    {
        // Logging de eliminación permanente
        \Log::warning("Sección de curso eliminada permanentemente: ID {$section->id}");
    }
}
