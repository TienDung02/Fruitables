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
        // Sửa giá từ USD sang VND: từ 500 VND đến 2000 VND
        $price = $this->faker->numberBetween(500, 2000);
        // Sale price giảm 10-30% so với giá gốc
        $salePrice = $this->faker->boolean(30) ? $price * $this->faker->randomFloat(2, 0.7, 0.9) : null;

        // ✅ Get random subcategory (Level 2) from existing categories
        $subcategory = Category::whereNotNull('parent_id')->inRandomOrder()->first();
        $categoryId = $subcategory ? $subcategory->id : 1;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(30) . ' Sản phẩm tươi ngon, chất lượng cao.',
            'short_description' => $this->faker->sentence(8),
            'price' => $price,
            'sale_price' => $salePrice,
            'sku' => strtoupper($this->faker->bothify('??###')),
            'stock_quantity' => $this->faker->numberBetween(10, 100),
            'category_id' => $categoryId, // ✅ Use existing subcategory
            'weight' => $this->faker->randomFloat(2, 0.1, 2.0),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'meta_title' => $name . ' - Fresh & Organic',
            'meta_description' => 'Buy fresh ' . strtolower($name) . ' online.',
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
            // Sửa sale price để phù hợp với VND và đảm bảo không vượt quá 2000 VND
            $salePrice = $attributes['price'] * $this->faker->randomFloat(2, 0.7, 0.9);
            return [
                'sale_price' => round($salePrice),
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
}
