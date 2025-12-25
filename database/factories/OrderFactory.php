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

        // Lấy user ngẫu nhiên từ database hoặc tạo mới nếu không có
        $user = User::inRandomOrder()->first();

        return [
            'id' => $this->generateUniqueOrderNumber(),
            'user_id' => $user->id,
            'status' => $this->faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED
            ]),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'payment_status' => $this->faker->randomElement([
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PAID,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_REFUNDED
            ]),
            'shipping_method' => $this->faker->randomElement(['free', 'standard', 'fast']),
            'customer_name' => $user->full_name ?: ($user->username ?: $user->email),
            'customer_email' => $user->emails,
            'customer_phone' => $user->phone ?? $this->faker->phoneNumber(),
            'customer_address' => $user->address ?? $this->faker->address(),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'payment_request_id' => $this->faker->optional(0.7)->uuid(),
            'payment_data' => $this->faker->optional(0.5)->randomElement([
                ['payment_method' => 'sepay', 'transaction_ref' => $this->faker->uuid()],
                ['payment_method' => 'cod', 'reference' => $this->faker->bothify('COD-####-****')],
                ['payment_method' => 'bank_transfer', 'bank_ref' => $this->faker->bothify('BT-########')],
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
        return $this->state(function (array $attributes) {
            $user = User::inRandomOrder()->first() ?? User::factory()->create();

            return [
                'user_id' => $user->id,
                // Sử dụng cùng logic an toàn như ở definition()
                'customer_name' => $user->full_name ?: ($user->username ?: $user->email),
                'customer_email' => $user->emails,
                'customer_phone' => $user->phone ?? fake()->phoneNumber(),
                'customer_address' => $user->address ?? fake()->address(),
            ];
        });
    }

    /**
     * Create order for guest checkout (no user_id).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->email(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_address' => fake()->address(),
        ]);
    }

    /**
     * Create order with SePay payment method.
     */
    public function sepayPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'payment_data' => [
                'payment_method' => 'sepay',
                'transaction_ref' => $this->faker->uuid(),
                'bank_code' => '970423',
                'account_number' => $this->faker->numerify('##########')
            ],
            'transaction_id' => $this->faker->uuid(),
            'paid_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create order with COD payment method.
     */
    public function codPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'payment_data' => [
                'payment_method' => 'cod',
                'reference' => $this->faker->bothify('COD-####-****')
            ],
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }
}
