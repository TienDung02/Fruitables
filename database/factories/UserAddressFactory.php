<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->optional(0.3)->company(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'postcode' => fake()->postcode(),
            'mobile' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'notes' => fake()->optional(0.2)->sentence(),
            'is_default' => fake()->boolean(20), // 20% chance of being default
            'type' => fake()->randomElement(['billing', 'shipping', 'both']),
        ];
    }

    /**
     * Indicate that the address is a default billing address.
     */
    public function defaultBilling(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'type' => 'billing',
        ]);
    }

    /**
     * Indicate that the address is a default shipping address.
     */
    public function defaultShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'type' => 'shipping',
        ]);
    }

    /**
     * Indicate that the address is for both billing and shipping.
     */
    public function both(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'both',
        ]);
    }
}
