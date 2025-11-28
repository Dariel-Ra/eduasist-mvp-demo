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
            'schedule_days' => $this->schedule_days,
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
 
            // Información formateada del horario
            'formatted_schedule_days' => $this->getFormattedScheduleDays(),
            'short_schedule_days' => $this->getShortScheduleDays(),
            'schedule_string' => $this->getScheduleString(),
            'short_schedule_string' => $this->getShortScheduleString(),
 
            // Estado de la sesión
            'is_in_session' => $this->isInSession(),
            'next_class_datetime' => $this->getNextClassDateTime()?->toISOString(),
 
            // Información de capacidad
            'enrolled_count' => $this->whenCounted('students', fn () => $this->students_count, fn () => $this->getEnrolledCount()),
            'available_seats' => $this->getAvailableSeats(),
            'has_available_seats' => $this->hasAvailableSeats(),
            'is_full' => $this->isFull(),
 
            // Relaciones cargadas
            'course' => $this->whenLoaded('course', fn () =>
                CourseResource::make($this->course)
            ),
 
            'teacher' => $this->whenLoaded('teacher', fn () =>
                TeacherResource::make($this->teacher)
            ),
 
            'students' => $this->whenLoaded('students', fn () =>
                StudentResource::collection($this->students)
            ),
 
            'students_with_pivot' => $this->whenLoaded('students', fn () =>
                $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'enrollment_code' => $student->enrollment_code,
                        'status' => $student->pivot->status,
                        'enrolled_at' => $student->pivot->created_at?->toISOString(),
                    ];
                })
            ),
 
            // Información completa para uso en formularios
            'full_name' => $this->getFullName(),
        ];
    }
 
    /**
     * Obtiene el nombre completo de la sección
     */
    private function getFullName(): string
    {
        $courseName = $this->whenLoaded('course',
            fn () => $this->course->name,
            fn () => 'Curso'
        );
 
        return sprintf(
            '%s - Sección %s',
            $courseName,
            $this->section ?? 'N/A'
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
