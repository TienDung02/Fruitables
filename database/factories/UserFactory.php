<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'username' => fake()->unique()->userName(),
            'full_name' => $firstName . ' ' . $lastName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'gender' => fake()->optional(0.7)->randomElement(['male', 'female', 'other']),
            'dob' => ($dob = fake()->optional(0.6)->dateTimeBetween('-60 years', '-18 years'))
                ? $dob->format('Y-m-d')
                : null,
            'avatar' => 'avatar/avatar_' . $this->faker->numberBetween(1, 50) . '.jpg',
            'is_active' => fake()->boolean(95), // 95% active users
            'email_verified_at' => fake()->boolean(80) ? now() : null, // 80% verified
            'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the user is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
        ]);
    }

    /**
     * Indicate that the user is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }

    /**
     * Indicate that the user has logged in recently.
     */
    public function recentLogin(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_login_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
