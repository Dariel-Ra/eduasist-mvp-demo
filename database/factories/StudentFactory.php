<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gradeLevels = [
            '1er Grado',
            '2do Grado',
            '3er Grado',
            '4to Grado',
            '5to Grado',
            '6to Grado',
            '1ro Secundaria',
            '2do Secundaria',
            '3ro Secundaria',
            '4to Secundaria',
            '5to Secundaria',
        ];

        $sections = ['A', 'B', 'C', 'D'];

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName() . ' ' . fake()->lastName(),
            'enrollment_code' => 'EST' . date('Y') . fake()->unique()->numberBetween(10000, 99999),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-5 years'),
            'grade_level' => fake()->randomElement($gradeLevels),
            'section' => fake()->randomElement($sections),
            'active' => fake()->boolean(90), // 90% activos
        ];
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the student should not have a date of birth.
     */
    public function withoutDateOfBirth(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => null,
        ]);
    }

    /**
     * Indicate that the student should not have an enrollment code.
     */
    public function withoutEnrollmentCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_code' => null,
        ]);
    }

    /**
     * Indicate that the student is in a specific grade.
     */
    public function inGrade(string $grade): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_level' => $grade,
        ]);
    }

    /**
     * Indicate that the student is in a specific section.
     */
    public function inSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }
}
