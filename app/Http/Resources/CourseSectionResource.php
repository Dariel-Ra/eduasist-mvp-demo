<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'section' => $this->section,
            'classroom' => $this->classroom,
            'max_students' => $this->max_students,
            'schedule_days' => $this->schedule_days, // Array de strings (valores del enum)
            'start_time' => $this->start_time?->format('H:i:s'), // Carbon to string
            'end_time' => $this->end_time?->format('H:i:s'), // Carbon to string
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),

            // Curso asociado (solo si está cargado)
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'code' => $this->course->code,
                    'grade_level' => $this->course->grade_level,
                ];
            }),

            // Profesor asociado (solo si está cargado)
            'teacher' => $this->whenLoaded('teacher', function () {
                return [
                    'id' => $this->teacher->id,
                    'code' => $this->teacher->code,
                    'specialty' => $this->teacher->specialty,
                    'name' => $this->teacher->user->name ?? null,
                    'email' => $this->teacher->user->email ?? null,
                ];
            }),

            // Estudiantes inscritos (solo si están cargados)
            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'enrollment_code' => $student->enrollment_code,
                        'status' => $student->pivot->status ?? null,
                    ];
                });
            }),

            // ==================== Campos Computados usando métodos del modelo ====================

            // Nombre para mostrar
            'display_name' => $this->section
                ? "{$this->course->name} - Sección {$this->section}"
                : $this->course->name,

            // Días de la semana formateados (Lun, Mar, Mié, etc.)
            'schedule_days_formatted' => $this->getFormattedScheduleDays(),

            // Días de la semana abreviados (Lun, Mar, Mié, etc.)
            'schedule_days_short' => $this->getShortScheduleDays(),

            // Horario completo: "Lunes, Miércoles, Viernes de 08:00 a 09:30"
            'schedule_full' => $this->getScheduleString(),

            // Horario corto: "Lun-Mié-Vie 08:00-09:30"
            'schedule_short' => $this->getShortScheduleString(),

            // Rango de tiempo simplificado: "08:00 - 09:30"
            'time_range' => $this->start_time && $this->end_time
                ? $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i')
                : null,

            // Estado
            'status_label' => $this->active ? 'Activa' : 'Inactiva',

            // Información del aula
            'has_classroom' => !is_null($this->classroom),

            // ==================== Información de estudiantes ====================

            // Cantidad de estudiantes inscritos
            'enrolled_count' => $this->getEnrolledCount(),

            // Cupos disponibles
            'available_seats' => $this->getAvailableSeats(),

            // Tiene cupos disponibles
            'has_available_seats' => $this->hasAvailableSeats(),

            // Está llena la sección
            'is_full' => $this->isFull(),

            // Tiene estudiantes inscritos (solo si students está cargado)
            'has_students' => $this->whenLoaded('students', function () {
                return $this->students->count() > 0;
            }),

            // Contador de estudiantes (si se usó withCount)
            'students_count' => $this->whenCounted('students'),

            // ==================== Estado en tiempo real ====================

            // ¿La clase está en sesión ahora?
            'is_in_session' => $this->isInSession(),

            // Fecha y hora de la próxima clase
            'next_class' => $this->getNextClassDateTime()?->toISOString(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
            ],
        ];
    }
}
