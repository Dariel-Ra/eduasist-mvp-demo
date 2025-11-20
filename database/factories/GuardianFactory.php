<?php

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Guardian::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = fake()->numerify('###-###-####');

        return [
            'user_id' => User::factory(),
            'personal_email' => fake()->unique()->safeEmail(),
            'phone_number' => $phone,
            'whatsapp_number' => fake()->boolean(70) ? $phone : fake()->numerify('###-###-####'),
        ];
    }

    /**
     * Indicate that the guardian should have a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the guardian should not have a personal email.
     */
    public function withoutPersonalEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'personal_email' => null,
        ]);
    }

    /**
     * Indicate that the guardian should not have a WhatsApp number.
     */
    public function withoutWhatsApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'whatsapp_number' => null,
        ]);
    }
}
