<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialties = [
            'Matemáticas',
            'Física',
            'Química',
            'Biología',
            'Historia',
            'Geografía',
            'Lengua y Literatura',
            'Inglés',
            'Educación Física',
            'Informática',
            'Arte',
            'Música',
        ];

        return [
            'user_id' => User::factory(),
            'code' => 'TCH' . fake()->unique()->numberBetween(1000, 9999),
            'specialty' => fake()->randomElement($specialties),
        ];
    }

    /**
     * Indicate that the teacher should have a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the teacher should not have a specialty.
     */
    public function withoutSpecialty(): static
    {
        return $this->state(fn (array $attributes) => [
            'specialty' => null,
        ]);
    }

    /**
     * Indicate that the teacher should not have a code.
     */
    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => null,
        ]);
    }
}
