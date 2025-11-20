<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 50 estudiantes con datos aleatorios
        Student::factory()
            ->count(50)
            ->create();
 
        // Crear algunos estudiantes específicos para testing
        Student::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Pérez García',
            'enrollment_code' => 'EST' . date('Y') . '00001',
            'grade_level' => '5to Grado',
            'section' => 'A',
            'active' => true,
        ]);
 
        Student::factory()->create([
            'first_name' => 'María',
            'last_name' => 'González López',
            'enrollment_code' => 'EST' . date('Y') . '00002',
            'grade_level' => '5to Grado',
            'section' => 'A',
            'active' => true,
        ]);
 
        // Crear algunos estudiantes inactivos
        Student::factory()
            ->inactive()
            ->count(5)
            ->create();
    }
}
