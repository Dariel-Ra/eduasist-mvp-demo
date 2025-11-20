<?php

namespace App\Observers;

use App\Models\GuardianStudent;
use Illuminate\Support\Facades\Log;

class GuardianStudentObserver
{
    /**
     * Handle the GuardianStudent "creating" event.
     */
    public function creating(GuardianStudent $guardianStudent): void
    {
        // Este método se ejecuta antes de crear el registro
    }

    /**
     * Handle the GuardianStudent "created" event.
     */
    public function created(GuardianStudent $guardianStudent): void
    {
        $relationship = $guardianStudent->relationship;
        $isPrimary = $guardianStudent->is_primary ? 'primario' : 'secundario';

        Log::info("Relación tutor-estudiante creada", [
            'guardian_id' => $guardianStudent->guardian_id,
            'student_id' => $guardianStudent->student_id,
            'relationship' => $relationship,
            'is_primary' => $guardianStudent->is_primary,
            'created_at' => $guardianStudent->created_at,
        ]);
    }

    /**
     * Handle the GuardianStudent "updating" event.
     */
    public function updating(GuardianStudent $guardianStudent): void
    {
        // Este método se ejecuta antes de actualizar el registro
    }

    /**
     * Handle the GuardianStudent "updated" event.
     */
    public function updated(GuardianStudent $guardianStudent): void
    {
        $changes = [];

        if ($guardianStudent->wasChanged('relationship')) {
            $changes['relationship'] = [
                'anterior' => $guardianStudent->getOriginal('relationship'),
                'nuevo' => $guardianStudent->relationship,
            ];
        }

        if ($guardianStudent->wasChanged('is_primary')) {
            $changes['is_primary'] = [
                'anterior' => $guardianStudent->getOriginal('is_primary'),
                'nuevo' => $guardianStudent->is_primary,
            ];
        }

        if (!empty($changes)) {
            Log::info("Relación tutor-estudiante actualizada", [
                'id' => $guardianStudent->id,
                'guardian_id' => $guardianStudent->guardian_id,
                'student_id' => $guardianStudent->student_id,
                'cambios' => $changes,
            ]);
        }
    }

    /**
     * Handle the GuardianStudent "deleted" event.
     */
    public function deleted(GuardianStudent $guardianStudent): void
    {
        Log::info("Relación tutor-estudiante eliminada", [
            'id' => $guardianStudent->id,
            'guardian_id' => $guardianStudent->guardian_id,
            'student_id' => $guardianStudent->student_id,
            'relationship' => $guardianStudent->relationship,
            'was_primary' => $guardianStudent->is_primary,
        ]);
    }

    /**
     * Handle the GuardianStudent "restored" event.
     */
    public function restored(GuardianStudent $guardianStudent): void
    {
        // No se usa soft deletes en este modelo
    }

    /**
     * Handle the GuardianStudent "force deleted" event.
     */
    public function forceDeleted(GuardianStudent $guardianStudent): void
    {
        // No se usa soft deletes en este modelo
    }
}
