<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'enrollment_code' => $this->enrollment_code,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'age' => $this->date_of_birth?->age,
            'grade_level' => $this->grade_level,
            'section' => $this->section,
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),

            // Tutores asociados
            'guardians' => $this->whenLoaded('guardians', function () {
                return $this->guardians->map(function ($guardian) {
                    return [
                        'id' => $guardian->id,
                        'name' => $guardian->user->name,
                        'email' => $guardian->user->email,
                        'phone_number' => $guardian->phone_number,
                        'relationship' => $guardian->pivot->relationship,
                        'is_primary' => $guardian->pivot->is_primary,
                    ];
                });
            }),
 
            // Secciones de cursos
            'sections' => $this->whenLoaded('sections', function () {
                return $this->sections->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'course_name' => $section->course->name,
                        'section' => $section->section,
                        'status' => $section->pivot->status,
                    ];
                });
            }),
 
            // Campos computados
            'display_info' => "{$this->full_name} ({$this->enrollment_code})",
            'grade_section' => $this->grade_level && $this->section
                ? "{$this->grade_level} - {$this->section}"
                : ($this->grade_level ?? 'Sin grado'),
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
