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
            'code'=> $this->code,
            'description' => $this->description,
            'grade_level' => $this->grade_level,
            'active' => $this->active,

            // Fechas (si en algún momento habilitas timestamps)
            'created_at'  => $this->created_at?->toISOString(),

            // Información derivada
            'full_name' => $this->getFullName(),

            // Conteo de secciones (solo si se cargaron)
            'sections_count' => $this->whenLoaded('sections', fn () => $this->sections->count()),

            // Relaciones
            'sections' => $this->whenLoaded('sections', fn () =>
                CourseSectionResource::collection($this->sections)
            ),
        ];
    }

    /**
     * Extra: Nombre combinado útil para selects.
     */
    private function getFullName(): string
    {
        return trim($this->code . ' - ' . $this->name);
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
            ],
        ];
    }
}
