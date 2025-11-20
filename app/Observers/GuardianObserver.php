<?php

namespace App\Observers;

use App\Models\Guardian;

class GuardianObserver
{
    /**
     * Handle the Guardian "created" event.
     */
    public function created(Guardian $guardian): void
    {
        \Log::info("Tutor creado: ID {$guardian->id} - Usuario: {$guardian->user->name}");
    }

    /**
     * Handle the Guardian "updated" event.
     */
    public function updated(Guardian $guardian): void
    {
        if($guardian->wasChanged("phone_number")) {
        \Log::info("Teléfono actualizado para tutor ID 
        {$guardian->id}: {$guardian->phone_number}");

        }
        if($guardian->wasChanged("whatsapp_number")) {
            \Log::info("Teléfono actualizado para tutor ID 
            {$guardian->id}: {$guardian->whatsapp_number}");
        }
    }

    /**
     * Handle the Guardian "deleted" event.
     */
    public function deleted(Guardian $guardian): void
    {
        \Log::info("Tutor eliminado: ID {$guardian->id}");
    }

    /**
     * Handle the Guardian "restored" event.
     */
    public function restored(Guardian $guardian): void
    {
         \Log::info("Tutor restaurado: ID {$guardian->id}");
    }

    /**
     * Handle the Guardian "force deleted" event.
     */
    public function forceDeleted(Guardian $guardian): void
    {
        \Log::warning("Tutor eliminado permanentemente: ID {$guardian->id}");
    }
}
