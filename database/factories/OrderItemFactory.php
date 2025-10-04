<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lấy một product variant ngẫu nhiên
        $productVariant = ProductVariant::inRandomOrder()->first();

        // Nếu không có product variant nào, tạo một cái mới
        if (!$productVariant) {
            $productVariant = ProductVariant::factory()->create();
        }

        // Tính giá dựa trên variant (ưu tiên sale_price nếu có)
        $price = $productVariant->sale_price ?? $productVariant->price;

        return [
            'order_id' => Order::factory(),
            'product_variant_id' => $productVariant->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $price,
        ];
    }

    /**
     * Configure the model factory to use existing orders.
     */
    public function forExistingOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => Order::inRandomOrder()->first()?->id ?? Order::factory(),
        ]);
    }

    /**
     * Configure the model factory to use existing product variants.
     */
    public function forExistingProductVariant(): static
    {
        return $this->state(function (array $attributes) {
            $productVariant = ProductVariant::inRandomOrder()->first();

            if (!$productVariant) {
                return $attributes;
            }

            return [
                'product_variant_id' => $productVariant->id,
                'price' => $productVariant->sale_price ?? $productVariant->price,
            ];
        });
    }

    /**
     * Set a specific quantity for the order item.
     */
    public function quantity(int $quantity): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $quantity,
        ]);
    }

    /**
     * Set a custom price for the order item.
     */
    public function price(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }
}
