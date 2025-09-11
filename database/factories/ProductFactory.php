<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Apple', 'Banana', 'Orange', 'Mango', 'Pineapple', 'Grapes', 'Strawberry',
            'Broccoli', 'Carrot', 'Blueberry', 'Tomato', 'Cucumber', 'Watermelon'
        ];

        $name = $this->faker->randomElement($products);
        $price = $this->faker->randomFloat(2, 5, 50);
        $salePrice = $this->faker->boolean(30) ? $price * 0.8 : null;

        // ✅ Get random subcategory (Level 2) from existing categories
        $subcategory = Category::whereNotNull('parent_id')->inRandomOrder()->first();
        $categoryId = $subcategory ? $subcategory->id : 1;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(30) . ' Sản phẩm tươi ngon, chất lượng cao.',
            'short_description' => $this->faker->sentence(8),
            'category_id' => $categoryId, // Use existing subcategory
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the Products is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the Products is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'sale_price' => $attributes['price'] * 0.8,
            ];
        });
    }

    /**
     * Indicate that the Products is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function ($product) {
            // Tạo biến thể mặc định cho mỗi sản phẩm
            \App\Models\ProductVariant::factory()->create([
                'product_id' => $product->id,
            ]);
        });
    }
}
