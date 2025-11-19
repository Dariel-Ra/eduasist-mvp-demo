<?php

namespace App\Observers;

use App\Models\Teacher;
use Illuminate\Support\Str;

class TeacherObserver
{
    /**
     * Handle the Teacher "creating" event.
     */
    public function creating(Teacher $teacher): void
    {
        // Generar código automáticamente si no se proporcionó
        if (empty($teacher->code)) {
            $teacher->code = $this->generateUniqueCode();
        }
    }

    /**
     * Handle the Teacher "created" event.
     */
    public function created(Teacher $teacher): void
    {
        // Aquí puedes agregar lógica después de crear un profesor
        // Por ejemplo: enviar email de bienvenida, crear registros relacionados, etc.

        // Ejemplo: Log de creación
        \Log::info("Profesor creado: {$teacher->code} - {$teacher->user->name}");
    }

    /**
     * Handle the Teacher "updated" event.
     */
    public function updated(Teacher $teacher): void
    {
        // Lógica después de actualizar un profesor
        // Por ejemplo: notificar cambios, actualizar cache, etc.

        if ($teacher->wasChanged('specialty')) {
            \Log::info("Especialidad actualizada para profesor {$teacher->code}: {$teacher->specialty}");
        }
    }

    /**
     * Handle the Teacher "deleted" event.
     */
    public function deleted(Teacher $teacher): void
    {
        // Lógica después de eliminar un profesor
        // Por ejemplo: limpiar datos relacionados, notificaciones, etc.

        \Log::info("Profesor eliminado: {$teacher->code}");
    }

    /**
     * Handle the Teacher "restored" event.
     */
    public function restored(Teacher $teacher): void
    {
        // Lógica después de restaurar un profesor eliminado
        \Log::info("Profesor restaurado: {$teacher->code}");
    }

    /**
     * Handle the Teacher "force deleted" event.
     */
    public function forceDeleted(Teacher $teacher): void
    {
        // Lógica después de eliminar permanentemente un profesor
        \Log::warning("Profesor eliminado permanentemente: {$teacher->code}");
    }

    /**
     * Generate a unique teacher code.
     */
    private function generateUniqueCode(): string
    {
        do {
            // Generar código en formato TCH + 4 dígitos
            $code = 'TCH' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Teacher::where('code', $code)->exists());

        return $code;
    }
}
