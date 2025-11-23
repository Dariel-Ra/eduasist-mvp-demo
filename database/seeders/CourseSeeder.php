<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear cursos básicos del sistema educativo dominicano
        $baseCourses = [
            [
                'name' => 'Matemáticas',
                'code' => 'MAT-001',
                'description' => 'Estudio de conceptos matemáticos fundamentales, álgebra, geometría y cálculo aplicado al nivel secundario.',
                'grade_level' => null, // Aplica a todos los grados
                'active' => true,
            ],
            [
                'name' => 'Lengua Española',
                'code' => 'LEN-001',
                'description' => 'Desarrollo de competencias lingüísticas y literarias en español, incluyendo gramática, ortografía y análisis literario.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Ciencias Sociales',
                'code' => 'SOC-001',
                'description' => 'Estudio de la sociedad, historia universal y dominicana, geografía y formación ciudadana.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Ciencias de la Naturaleza',
                'code' => 'NAT-001',
                'description' => 'Exploración de fenómenos naturales, biología, física y química básica.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Inglés',
                'code' => 'ING-001',
                'description' => 'Desarrollo de habilidades comunicativas en inglés: speaking, listening, reading y writing.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Educación Física',
                'code' => 'EDF-001',
                'description' => 'Promoción de la actividad física, deportes y desarrollo de habilidades motoras.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Formación Integral Humana y Religiosa',
                'code' => 'FIH-001',
                'description' => 'Formación en valores éticos, morales y espirituales para el desarrollo integral del estudiante.',
                'grade_level' => null,
                'active' => true,
            ],
            [
                'name' => 'Informática',
                'code' => 'INF-001',
                'description' => 'Introducción a la tecnología, ofimática, programación básica y ciudadanía digital.',
                'grade_level' => null,
                'active' => true,
            ],
        ];

        foreach ($baseCourses as $courseData) {
            Course::create($courseData);
        }
 
        // Crear cursos específicos de grados superiores
        $advancedCourses = [
            [
                'name' => 'Física',
                'code' => 'FIS-001',
                'description' => 'Estudio de las leyes y principios que rigen el universo físico: mecánica, termodinámica, electricidad y magnetismo.',
                'grade_level' => '4to Secundaria',
                'active' => true,
            ],
            [
                'name' => 'Química',
                'code' => 'QUI-001',
                'description' => 'Estudio de la composición, estructura, propiedades y transformaciones de la materia.',
                'grade_level' => '4to Secundaria',
                'active' => true,
            ],
            [
                'name' => 'Biología',
                'code' => 'BIO-001',
                'description' => 'Estudio profundo de los seres vivos, sus procesos vitales, genética y ecología.',
                'grade_level' => '5to Secundaria',
                'active' => true,
            ],
        ];
 
        foreach ($advancedCourses as $courseData) {
            Course::create($courseData);
        }
 
        // Crear un curso de ejemplo para testing/desarrollo
        Course::create([
            'name' => 'Curso de Prueba',
            'code' => 'TEST-001',
            'description' => 'Este es un curso de ejemplo para pruebas y desarrollo del sistema.',
            'grade_level' => '1ro Secundaria',
            'active' => false, // Inactivo por defecto
        ]);
 
        // Generar 15 cursos adicionales aleatorios usando la factory
        Course::factory()
            ->count(15)
            ->create();
    }
}
