<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'late_threshold_minutes' => $this->late_threshold_minutes,
            'auto_notify_parents' => $this->auto_notify_parents,
            'notification_delay_minutes' => $this->notification_delay_minutes,
            'created_at' => $this->created_at?->toISOString(),
 
            // Información formateada
            'late_threshold_formatted' => $this->getLateThresholdFormatted(),
            'notification_delay_formatted' => $this->getNotificationDelayFormatted(),
            'auto_notify_status' => $this->auto_notify_parents ? 'Activado' : 'Desactivado',
 
            // Información adicional útil
            'late_threshold_display' => sprintf('%d minutos', $this->late_threshold_minutes),
            'notification_delay_display' => sprintf('%d minutos', $this->notification_delay_minutes),
 
            // Configuraciones recomendadas
            'is_using_defaults' => $this->isUsingDefaults(),
            'recommendations' => $this->getRecommendations(),
        ];
    }
 
    /**
     * Obtiene el umbral de tardanza formateado
     */
    private function getLateThresholdFormatted(): string
    {
        $minutes = $this->late_threshold_minutes;
 
        if ($minutes < 60) {
            return sprintf('%d minuto%s', $minutes, $minutes !== 1 ? 's' : '');
        }
 
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
 
        if ($remainingMinutes === 0) {
            return sprintf('%d hora%s', $hours, $hours !== 1 ? 's' : '');
        }
 
        return sprintf('%d hora%s y %d minuto%s',
            $hours,
            $hours !== 1 ? 's' : '',
            $remainingMinutes,
            $remainingMinutes !== 1 ? 's' : ''
        );
    }
 
    /**
     * Obtiene el retraso de notificación formateado
     */
    private function getNotificationDelayFormatted(): string
    {
        $minutes = $this->notification_delay_minutes;
 
        if ($minutes < 60) {
            return sprintf('%d minuto%s', $minutes, $minutes !== 1 ? 's' : '');
        }
 
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
 
        if ($remainingMinutes === 0) {
            return sprintf('%d hora%s', $hours, $hours !== 1 ? 's' : '');
        }
 
        return sprintf('%d hora%s y %d minuto%s',
            $hours,
            $hours !== 1 ? 's' : '',
            $remainingMinutes,
            $remainingMinutes !== 1 ? 's' : ''
        );
    }
 
    /**
     * Verifica si está usando los valores por defecto
     */
    private function isUsingDefaults(): bool
    {
        return $this->late_threshold_minutes === 15
            && $this->auto_notify_parents === true
            && $this->notification_delay_minutes === 30;
    }
 
    /**
     * Obtiene recomendaciones de configuración
     */
    private function getRecommendations(): array
    {
        $recommendations = [];
 
        // Recomendación de umbral de tardanza
        if ($this->late_threshold_minutes < 5) {
            $recommendations[] = [
                'field' => 'late_threshold_minutes',
                'type' => 'warning',
                'message' => 'El umbral de tardanza es muy bajo. Se recomienda al menos 5 minutos para evitar falsas tardanzas.',
            ];
        } elseif ($this->late_threshold_minutes > 30) {
            $recommendations[] = [
                'field' => 'late_threshold_minutes',
                'type' => 'info',
                'message' => 'El umbral de tardanza es alto. Considere reducirlo para un control más estricto.',
            ];
        }
 
        // Recomendación de retraso de notificación
        if ($this->notification_delay_minutes < 15) {
            $recommendations[] = [
                'field' => 'notification_delay_minutes',
                'type' => 'warning',
                'message' => 'El retraso de notificación es muy corto. Los tutores podrían recibir notificaciones innecesarias.',
            ];
        } elseif ($this->notification_delay_minutes > 60) {
            $recommendations[] = [
                'field' => 'notification_delay_minutes',
                'type' => 'info',
                'message' => 'El retraso de notificación es largo. Los tutores podrían ser notificados muy tarde.',
            ];
        }
 
        // Recomendación de notificación automática
        if (!$this->auto_notify_parents) {
            $recommendations[] = [
                'field' => 'auto_notify_parents',
                'type' => 'info',
                'message' => 'La notificación automática está desactivada. Los tutores no recibirán alertas automáticas.',
            ];
        }
 
        return $recommendations;
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
                'is_singleton' => true,
            ],
        ];
    }
}
