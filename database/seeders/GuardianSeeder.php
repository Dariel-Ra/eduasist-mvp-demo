<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Database\Seeder;

class GuardianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 15 tutores con usuarios
        Guardian::factory()
            ->count(15)
            ->create();

        // Ejemplo: crear un tutor específico para testing
        $guardianUser = User::factory()->create([
            'name' => 'Tutor Demo',
            'email' => 'guardian@example.com',
            'password' => 'password',
        ]);

        Guardian::factory()
            ->forUser($guardianUser)
            ->create([
                'personal_email' => 'tutor.personal@example.com',
                'phone_number' => '555-0001',
                'whatsapp_number' => '555-0001',
            ]);
    }
}
