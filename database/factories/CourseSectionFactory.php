<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseSection;
use App\Models\Course;
use App\Models\Teacher;
use App\Enums\ScheduleDay;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseSection>
 */
class CourseSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Secciones comunes
        $sections = ['A', 'B', 'C', 'D', 'E'];

        // Aulas comunes
        $classrooms = [
            '101', '102', '103', '104', '105',
            '201', '202', '203', '204', '205',
            '301', '302', '303', '304', '305',
            'Lab 1', 'Lab 2', 'Auditorio', 'Gimnasio',
        ];

        // Combinaciones comunes de días usando el enum
        $scheduleDaysCombinations = [
            [ScheduleDay::MONDAY->value, ScheduleDay::WEDNESDAY->value, ScheduleDay::FRIDAY->value],
            [ScheduleDay::TUESDAY->value, ScheduleDay::THURSDAY->value],
            [ScheduleDay::MONDAY->value, ScheduleDay::WEDNESDAY->value],
            [ScheduleDay::TUESDAY->value, ScheduleDay::THURSDAY->value, ScheduleDay::FRIDAY->value],
            ScheduleDay::values(), // Diario
        ];

        // Horarios comunes (formato 24 horas)
        $startTimes = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00'];
        $startTime = fake()->randomElement($startTimes);

        // Calcular hora de fin (1-2 horas después)
        $duration = fake()->randomElement([1, 2]); // horas
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 3600));

        return [
            'course_id' => Course::factory(),
            'teacher_id' => Teacher::factory(),
            'section' => fake()->randomElement($sections),
            'classroom' => fake()->randomElement($classrooms),
            'max_students' => fake()->numberBetween(20, 40),
            'schedule_days' => fake()->randomElement($scheduleDaysCombinations),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'active' => fake()->boolean(90), // 90% activas
        ];
    }

    /**
     * Indicate that the section should be for a specific course.
     */
    public function forCourse(Course $course): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
        ]);
    }

    /**
     * Indicate that the section should have a specific teacher.
     */
    public function withTeacher(Teacher $teacher): static
    {
        return $this->state(fn (array $attributes) => [
            'teacher_id' => $teacher->id,
        ]);
    }

    /**
     * Indicate that the section should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the section should have a specific section letter.
     */
    public function withSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }

    /**
     * Indicate that the section should have a specific classroom.
     */
    public function inClassroom(string $classroom): static
    {
        return $this->state(fn (array $attributes) => [
            'classroom' => $classroom,
        ]);
    }

    /**
     * Indicate that the section should have a specific schedule.
     */
    public function withSchedule(array $days, string $startTime, string $endTime): static
    {
        return $this->state(fn (array $attributes) => [
            'schedule_days' => $days,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    /**
     * Indicate that the section should be for morning classes.
     */
    public function morningClasses(): static
    {
        $startTime = fake()->randomElement(['08:00:00', '09:00:00', '10:00:00']);
        $endTime = date('H:i:s', strtotime($startTime) + 3600); // +1 hora

        return $this->state(fn (array $attributes) => [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    /**
     * Indicate that the section should be for afternoon classes.
     */
    public function afternoonClasses(): static
    {
        $startTime = fake()->randomElement(['13:00:00', '14:00:00', '15:00:00']);
        $endTime = date('H:i:s', strtotime($startTime) + 3600); // +1 hora

        return $this->state(fn (array $attributes) => [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    /**
     * Indicate that the section should have daily classes.
     */
    public function dailyClasses(): static
    {
        return $this->state(fn (array $attributes) => [
            'schedule_days' => ScheduleDay::values(),
        ]);
    }

    /**
     * Indicate that the section should have a small capacity.
     */
    public function smallCapacity(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_students' => fake()->numberBetween(10, 20),
        ]);
    }

    /**
     * Indicate that the section should have a large capacity.
     */
    public function largeCapacity(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_students' => fake()->numberBetween(35, 50),
        ]);
    }
}
