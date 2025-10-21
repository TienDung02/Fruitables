<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Ward;
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
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'ward_id' => Ward::inRandomOrder()->first()?->id ?? null,
            'label' => fake()->randomElement(['home', 'work', 'other']),
            'is_default' => fake()->boolean(20), // 20% chance of being default
        ];
    }

    /**
     * Indicate that the address is default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the address is home address.
     */
    public function home(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'home',
        ]);
    }

    /**
     * Indicate that the address is work address.
     */
    public function work(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'work',
        ]);
    }

    /**
     * Indicate that the address is other address.
     */
    public function other(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'other',
        ]);
    }
}
