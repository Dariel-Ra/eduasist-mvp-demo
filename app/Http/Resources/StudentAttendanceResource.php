<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
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
            'course_section_id' => $this->course_section_id,
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'date' => $this->date?->toDateString(),
            'check_in_time' => $this->check_in_time?->format('H:i:s'),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
 
            // Estado formateado
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
 
            // Información temporal
            'date_formatted' => $this->date?->format('d/m/Y'),
            'day_name' => $this->date?->locale('es')->isoFormat('dddd'),
            'is_today' => $this->date?->isToday() ?? false,
            'is_late' => $this->status === 'late',
            'is_absent' => $this->status === 'absent',
            'is_present' => $this->status === 'present',
            'is_excused' => $this->status === 'excused',
 
            // Relaciones cargadas
            'course_section' => $this->whenLoaded('courseSection', fn () =>
                CourseSectionResource::make($this->courseSection)
            ),
 
            'student' => $this->whenLoaded('student', fn () =>
                StudentResource::make($this->student)
            ),
 
            'teacher' => $this->whenLoaded('teacher', fn () =>
                TeacherResource::make($this->teacher)
            ),
 
            'notifications' => $this->whenLoaded('notifications', fn () =>
                GuardianNotificationResource::collection($this->notifications)
            ),
 
            'notifications_count' => $this->whenCounted('notifications'),
 
            // Información completa para listados
            'display_info' => $this->getDisplayInfo(),
        ];
    }
 
    /**
     * Obtiene la etiqueta del estado en español
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'present' => 'Presente',
            'late' => 'Tardanza',
            'absent' => 'Ausente',
            'excused' => 'Justificado',
            default => 'Desconocido',
        };
    }

    /**
     * Obtiene el color asociado al estado
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'excused' => 'info',
            default => 'secondary',
        };
    }
 
    /**
     * Obtiene información para mostrar en listados
     */
    private function getDisplayInfo(): string
    {
        $studentName = $this->whenLoaded('student',
            fn () => $this->student->full_name,
            fn () => 'Estudiante'
        );
 
        $courseName = $this->whenLoaded('courseSection',
            fn () => $this->courseSection->course->name ?? 'Curso',
            fn () => 'Curso'
        );
 
        return sprintf(
            '%s - %s - %s (%s)',
            $studentName,
            $courseName,
            $this->date?->format('d/m/Y') ?? 'Fecha',
            $this->getStatusLabel()
        );
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
