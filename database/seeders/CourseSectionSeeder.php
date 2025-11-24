<?php

namespace Database\Seeders;

use App\Models\CourseSection;
use App\Models\Course;
use App\Models\Teacher;
use App\Enums\ScheduleDay;
use Illuminate\Database\Seeder;

class CourseSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener cursos y profesores existentes
        $courses = Course::where('active', true)->get();
        $teachers = Teacher::all();

        if ($courses->isEmpty() || $teachers->isEmpty()) {
            $this->command->warn('No hay cursos o profesores disponibles. Ejecuta CourseSeeder y TeacherSeeder primero.');
            return;
        }

        // Crear secciones básicas para cada curso
        foreach ($courses->take(5) as $course) {
            // Sección A - Turno matutino
            CourseSection::create([
                'course_id' => $course->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'A',
                'classroom' => '101',
                'max_students' => 30,
                'schedule_days' => [ScheduleDay::MONDAY->value, ScheduleDay::WEDNESDAY->value, ScheduleDay::FRIDAY->value],
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'active' => true,
            ]);

            // Sección B - Turno matutino
            CourseSection::create([
                'course_id' => $course->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'B',
                'classroom' => '102',
                'max_students' => 30,
                'schedule_days' => [ScheduleDay::TUESDAY->value, ScheduleDay::THURSDAY->value],
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'active' => true,
            ]);

            // Sección C - Turno vespertino
            CourseSection::create([
                'course_id' => $course->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'C',
                'classroom' => '201',
                'max_students' => 25,
                'schedule_days' => [ScheduleDay::MONDAY->value, ScheduleDay::WEDNESDAY->value, ScheduleDay::FRIDAY->value],
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'active' => true,
            ]);
        }

        // Crear secciones específicas de ejemplo
        $mathematicsCourse = Course::where('code', 'MAT-001')->first();
        $englishCourse = Course::where('code', 'ING-001')->first();

        if ($mathematicsCourse) {
            CourseSection::create([
                'course_id' => $mathematicsCourse->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'Avanzada',
                'classroom' => 'Lab 1',
                'max_students' => 20,
                'schedule_days' => [ScheduleDay::TUESDAY->value, ScheduleDay::THURSDAY->value, ScheduleDay::FRIDAY->value],
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'active' => true,
            ]);
        }

        if ($englishCourse) {
            CourseSection::create([
                'course_id' => $englishCourse->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'Conversación',
                'classroom' => '305',
                'max_students' => 15,
                'schedule_days' => [ScheduleDay::MONDAY->value, ScheduleDay::WEDNESDAY->value],
                'start_time' => '15:00:00',
                'end_time' => '16:30:00',
                'active' => true,
            ]);
        }

        // Crear una sección inactiva para testing
        if ($courses->count() > 0) {
            CourseSection::create([
                'course_id' => $courses->first()->id,
                'teacher_id' => $teachers->random()->id,
                'section' => 'X',
                'classroom' => '999',
                'max_students' => 10,
                'schedule_days' => [ScheduleDay::FRIDAY->value],
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'active' => false,
            ]);
        }

        // Generar secciones adicionales aleatorias usando la factory
        CourseSection::factory()
            ->count(20)
            ->create();

        $this->command->info('Secciones de cursos creadas exitosamente.');
    }
}
