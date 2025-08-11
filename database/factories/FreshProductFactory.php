<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FreshProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        $freshProducts = [
            'apple', 'banana', 'blueberry', 'broccoli', 'carrot', 'cucumber',
            'grapes', 'mango', 'orange', 'pineapple', 'strawberry', 'tomato', 'watermelon'
        ];

        $name = $this->faker->randomElement($freshProducts);
        $price = $this->faker->randomFloat(2, 5, 35);

        $vegetables = ['broccoli', 'carrot', 'cucumber', 'tomato'];
        $isVegetable = in_array($name, $vegetables);

        return [
            'name' => 'Fresh ' . ucfirst($name),
            'slug' => Str::slug('fresh-' . $name),
            'description' => "Farm-fresh {$name} harvested at peak ripeness. " .
                ($isVegetable ? "Grown using sustainable farming practices without harmful pesticides. Perfect for healthy cooking and nutritious meals."
                    : "Naturally sweet and packed with vitamins, minerals, and antioxidants. Perfect for snacking, smoothies, and desserts."),
            'short_description' => "Farm-fresh {$name} - naturally grown, peak quality",
            'price' => $price,
            'sale_price' => $this->faker->boolean(25) ? $price * 0.9 : null,
            'sku' => ($isVegetable ? 'VEG-' : 'FRT-') . strtoupper($this->faker->bothify('??###')),
            'stock_quantity' => $this->faker->numberBetween(30, 150),
            'category_id' => Category::where('slug', $isVegetable ? 'fresh-vegetables' : 'fresh-fruits')->first()?->id ?? 1,
            'weight' => $this->faker->randomFloat(2, 0.2, 3.0),
            'is_featured' => $this->faker->boolean(30),
            'is_active' => true,
            'meta_title' => "Fresh {$name} - Organic & Natural",
            'meta_description' => "Buy fresh organic {$name} online. Farm-to-table quality, naturally grown.",
        ];
    }

    public function getImagePath($productName): string
    {
        $name = str_replace(['fresh-', 'fresh '], '', strtolower($productName));
        return "fruit/{$name}";
    }
}
