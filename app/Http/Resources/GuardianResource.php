<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
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
            'personal_email' => $this->personal_email,
            'phone_number' => $this->phone_number,
            'whatsapp_number' => $this->whatsapp_number,
            'created_at' => $this->created_at?->toISOString(),
 
            // Información del usuario relacionado
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'email_verified_at' => $this->user->email_verified_at?->toISOString(),
            ],
 
            // Estudiantes asociados
            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'relationship' => $student->pivot->relationship,
                        'is_primary' => $student->pivot->is_primary,
                    ];
                });
            }),
 
            // Campos computados
            'display_name' => $this->user->name,
            'contact_info' => $this->phone_number ?? 
            $this->whatsapp_number ?? 
            $this->personal_email ?? 'Sin información de contacto',
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
