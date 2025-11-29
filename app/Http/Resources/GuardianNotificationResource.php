<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianNotificationResource extends JsonResource
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
            'attendance_id' => $this->attendance_id,
            'guardian_id' => $this->guardian_id,
            'type' => $this->type,
            'method' => $this->method,
            'message' => $this->message,
            'status' => $this->status,
            'sent_at' => $this->sent_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
 
            // Información formateada
            'type_label' => $this->getTypeLabel(),
            'method_label' => $this->getMethodLabel(),
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
 
            // Estados booleanos
            'is_pending' => $this->status === 'pending',
            'is_sent' => $this->status === 'sent',
            'is_failed' => $this->status === 'failed',
 
            // Información temporal
            'sent_at_formatted' => $this->sent_at?->format('d/m/Y H:i:s'),
            'sent_at_human' => $this->sent_at?->locale('es')->diffForHumans(),
            'was_sent_today' => $this->sent_at?->isToday() ?? false,
 
            // Relaciones cargadas
            'attendance' => $this->whenLoaded('attendance', fn () =>
                StudentAttendanceResource::make($this->attendance)
            ),
 
            'guardian' => $this->whenLoaded('guardian', fn () =>
                GuardianResource::make($this->guardian)
            ),
 
            // Información completa para listados
            'display_info' => $this->getDisplayInfo(),
        ];
    }
 
    /**
     * Obtiene la etiqueta del tipo en español
     */
    private function getTypeLabel(): string
    {
        return match($this->type) {
            'late' => 'Tardanza',
            'absent' => 'Ausencia',
            'excused' => 'Justificación',
            default => 'Desconocido',
        };
    }
 
    /**
     * Obtiene la etiqueta del método en español
     */
    private function getMethodLabel(): string
    {
        return match($this->method) {
            'email' => 'Correo electrónico',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
            default => 'Desconocido',
        };
    }
 
    /**
     * Obtiene la etiqueta del estado en español
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'sent' => 'Enviado',
            'failed' => 'Fallido',
            default => 'Desconocido',
        };
    }
 
    /**
     * Obtiene el color asociado al estado
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'sent' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }
 
    /**
     * Obtiene información para mostrar en listados
     */
    private function getDisplayInfo(): string
    {
        $guardianName = $this->whenLoaded('guardian',
            fn () => $this->guardian->user->name ?? 'Tutor',
            fn () => 'Tutor'
        );

        $studentName = $this->whenLoaded('attendance',
            fn () => $this->attendance->student->full_name ?? 'Estudiante',
            fn () => 'Estudiante'
        );
 
        return sprintf(
            '%s - %s - %s (%s)',
            $guardianName,
            $studentName,
            $this->getTypeLabel(),
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
