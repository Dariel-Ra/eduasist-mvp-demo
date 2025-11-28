<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionStudentResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
 
            // Estado formateado
            'status_label' => $this->getStatusLabel(),
            'is_active' => $this->status === 'active',
            'is_dropped' => $this->status === 'dropped',
 
            // Relaciones cargadas
            'course_section' => $this->whenLoaded('courseSection', fn () =>
                CourseSectionResource::make($this->courseSection)
            ),
 
            'student' => $this->whenLoaded('student', fn () =>
                StudentResource::make($this->student)
            ),
 
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
            'active' => 'Activo',
            'dropped' => 'Retirado',
            default => 'Desconocido',
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
 
        $sectionName = $this->whenLoaded('courseSection',
            fn () => $this->courseSection->section ?? 'N/A',
            fn () => 'Sección'
        );
 
        return sprintf(
            '%s - Sección %s (%s)',
            $studentName,
            $sectionName,
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
