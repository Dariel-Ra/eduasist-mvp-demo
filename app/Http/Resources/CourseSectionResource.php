<?php
// app/Http/Resources/CourseSectionResource.php

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
            'schedule_days' => $this->schedule_days, // Array de strings
            'schedule_days_formatted' => $this->getFormattedScheduleDays(), // Array de labels en español
            'schedule_days_short' => $this->getShortScheduleDays(), // Array de labels cortos
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
            
            // Información adicional
            'schedule_string' => $this->getScheduleString(),
            'schedule_string_short' => $this->getShortScheduleString(),
            'is_in_session' => $this->isInSession(),
            'enrolled_count' => $this->whenLoaded('students', fn() => $this->students->count()),
            'available_seats' => $this->getAvailableSeats(),
            'has_available_seats' => $this->hasAvailableSeats(),
            'is_full' => $this->isFull(),
            'next_class' => $this->getNextClassDateTime()?->toISOString(),
            
            // Relaciones
            'course' => $this->whenLoaded('course', fn() => CourseResource::make($this->course)),
            'teacher' => $this->whenLoaded('teacher', fn() => TeacherResource::make($this->teacher)),
            'students' => $this->whenLoaded('students', fn() => StudentResource::collection($this->students)),
        ];
    }
//             'course' => $this->whenLoaded('course', fn() => CourseResource::make($this->course)),
//             'teacher' => $this->whenLoaded('teacher', fn() => TeacherResource::make($this->teacher)),
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