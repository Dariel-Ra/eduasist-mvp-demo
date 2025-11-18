<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Teacher;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
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
            'Lengua Española y literatura',
            'Inglés',
            'Educación Física',
            'Informática',
            'Arte',
            'Música',
        ];

        return [
            'user_id' => User::factory(),
            'code'=> 'TCH' . fake()->unique()->numberBetween(1000, 9999),
            'speciality'=> fake()->randomElement($specialties),
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
