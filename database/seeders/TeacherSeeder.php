<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder

{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 10 profesores con usuarios
        Teacher::factory()
            ->count(10)
            ->create();
 
        // Ejemplo: crear un profesor específico para testing
        $teacherUser = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'teacher@example.com',
            'password' => 'password',
        ]);
 
        Teacher::factory()
            ->forUser($teacherUser)
            ->create([
                'code' => 'TCH0001',
                'specialty' => 'Matemáticas',
            ]);
    }
}