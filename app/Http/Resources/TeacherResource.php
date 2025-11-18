<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
class TeacherResource extends JsonResource
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
            'code' => $this->code,
            'specialty' => $this->specialty,
            'created_at' => $this->created_at?->toISOString(),
 
            // Información del usuario relacionado
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'email_verified_at' => $this->user->email_verified_at?->toISOString(),
            ],
 
            // Campos computados
            'display_name' => $this->user->name,
            'full_code' => $this->code ?? 'Sin código',
            'has_specialty' => !is_null($this->specialty),
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