<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(5000, 50000) / 100; // 50.00 - 500.00
        $shippingCost = $this->faker->randomElement([0, 500, 1000, 1500]) / 100; // Free, 5.00, 10.00, 15.00
        $total = $subtotal + $shippingCost;

        return [
            'id' => $this->generateUniqueOrderNumber(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement([
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ]),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'payment_method' => $this->faker->randomElement(['cod', 'momo', 'card']),
            'payment_status' => $this->faker->randomElement([
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PAID,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_REFUNDED
            ]),
            'shipping_method' => $this->faker->randomElement(['standard', 'express', 'overnight']),
            'customer_info' => [
                'name' => $this->faker->name(),
                'email' => $this->faker->email(),
                'phone' => $this->faker->phoneNumber(),
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'postal_code' => $this->faker->postcode(),
                'country' => $this->faker->country(),
            ],
            'notes' => $this->faker->optional(0.3)->sentence(),
            'payment_request_id' => $this->faker->optional(0.7)->uuid(),
            'payment_data' => $this->faker->optional(0.5)->randomElement([
                ['transaction_ref' => $this->faker->uuid()],
                ['card_last_four' => $this->faker->numerify('****')],
                null
            ]),
            'transaction_id' => $this->faker->optional(0.6)->uuid(),
            'paid_at' => $this->faker->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }

    /**
     * Generate a truly unique order number with microseconds
     */
    private function generateUniqueOrderNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $microseconds = substr(microtime(), 2, 6); // Get microseconds
        $random = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        return 'ORD-' . $timestamp . $microseconds . '-' . $random;
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the order is completed and paid.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_DELIVERED,
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'paid_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_CANCELLED,
            'payment_status' => Order::PAYMENT_STATUS_FAILED,
            'paid_at' => null,
        ]);
    }

    /**
     * Configure the model factory to use existing users.
     */
    public function forExistingUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ]);
    }
}
