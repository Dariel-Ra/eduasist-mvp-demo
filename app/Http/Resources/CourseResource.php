<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'grade_level' => $this->grade_level,
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),

            // Secciones asociadas (solo si están cargadas)
            'sections' => $this->whenLoaded('sections', function () {
                return $this->sections->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'section' => $section->section,
                        'classroom' => $section->classroom,
                        'max_students' => $section->max_students,
                        'teacher_name' => $section->teacher->user->name ?? null,
                        'active' => $section->active,
                    ];
                });
            }),

            // Campos computados
            'display_name' => $this->code
                ? "{$this->name} ({$this->code})"
                : $this->name,
            'full_code' => $this->code ?? 'Sin código',
            'has_description' => !is_null($this->description),
            'status_label' => $this->active ? 'Activo' : 'Inactivo',
            'sections_count' => $this->whenCounted('sections'),
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
