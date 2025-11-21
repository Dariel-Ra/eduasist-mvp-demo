<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianStudentResource extends JsonResource
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
            'guardian_id' => $this->guardian_id,
            'student_id' => $this->student_id,
            'relationship' => $this->relationship,
            'relationship_label' => $this->getRelationshipLabel(),
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
 
            // Relaciones opcionales
            'guardian' => new GuardianResource($this->whenLoaded('guardian')),
            'student' => new StudentResource($this->whenLoaded('student')),
        ]; 
    }

    /**
     * Get human-readable relationship label.
     *
     * @return string
     */
    private function getRelationshipLabel(): string
    {
        return match($this->relationship) {
            'father' => 'Padre',
            'mother' => 'Madre',
            'guardian' => 'Tutor',
            'other' => 'Otro',
            default => ucfirst($this->relationship),
        };
    }
}
