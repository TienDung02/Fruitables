<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DriedProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        // Berries subcategory
        $berries = ['blueberries', 'cranberries', 'strawberries'];

        // Citrus subcategory
        $citrus = ['lemon', 'orange', 'tangerine'];

        // Others subcategory
        $others = ['apple', 'fig', 'pear', 'raisins'];

        // Stone subcategory
        $stone = ['apricots', 'cherries', 'dates', 'plums'];

        // Tropical subcategory
        $tropical = ['banana', 'coconut', 'mango', 'papaya', 'pineapple'];

        $allDried = array_merge($berries, $citrus, $others, $stone, $tropical);
        $name = $this->faker->randomElement($allDried);
        $price = $this->faker->randomFloat(2, 15, 80); // Dried products are more expensive

        // Determine category based on product type
        $categorySlug = 'other-dried-fruits'; // default
        if (in_array($name, $berries)) {
            $categorySlug = 'dried-berries';
        } elseif (in_array($name, $citrus)) {
            $categorySlug = 'dried-citrus';
        } elseif (in_array($name, $stone)) {
            $categorySlug = 'dried-stone-fruits';
        } elseif (in_array($name, $tropical)) {
            $categorySlug = 'dried-tropical';
        }

        return [
            'name' => 'Dried ' . ucfirst($name),
            'slug' => Str::slug('dried-' . $name),
            'description' => "Premium dried {$name} with no added sugars or preservatives. Naturally dehydrated to preserve maximum nutrition and flavor. Perfect for healthy snacking, baking, and cooking. Rich in vitamins, minerals, and antioxidants.",
            'short_description' => "Natural dried {$name} - no additives, pure nutrition",
            'price' => $price,
            'sale_price' => $this->faker->boolean(20) ? $price * 0.85 : null,
            'sku' => 'DRD-' . strtoupper($this->faker->bothify('??###')),
            'stock_quantity' => $this->faker->numberBetween(20, 100),
            'category_id' => Category::where('slug', $categorySlug)->first()?->id ?? 1,
            'weight' => $this->faker->randomFloat(2, 0.25, 1.0),
            'is_featured' => $this->faker->boolean(25),
            'is_active' => true,
            'meta_title' => "Dried {$name} - Premium Quality Natural",
            'meta_description' => "Buy premium dried {$name} online. No additives, naturally dehydrated, rich in nutrients.",
        ];
    }

    // Helper method to get image path based on product name
    public function getImagePath($productName): string
    {
        $name = str_replace(['dried-', 'dried '], '', strtolower($productName));

        // Map to correct folder structure
        $folderMap = [
            'blueberries' => 'dried/Berries/blueberries',
            'cranberries' => 'dried/Berries/cranberries',
            'strawberries' => 'dried/Berries/strawberries',
            'lemon' => 'dried/Citrus/lemon',
            'orange' => 'dried/Citrus/orange',
            'tangerine' => 'dried/Citrus/tangerine',
            'apple' => 'dried/Others/apple',
            'fig' => 'dried/Others/fig',
            'pear' => 'dried/Others/pear',
            'raisins' => 'dried/Others/raisins',
            'apricots' => 'dried/Stone/apricots',
            'cherries' => 'dried/Stone/cherries',
            'dates' => 'dried/Stone/dates',
            'plums' => 'dried/Stone/plums',
            'banana' => 'dried/Tropical/banana',
            'coconut' => 'dried/Tropical/coconut',
            'mango' => 'dried/Tropical/mango',
            'papaya' => 'dried/Tropical/papaya',
            'pineapple' => 'dried/Tropical/pineapple',
        ];

        return $folderMap[$name] ?? 'dried/Others/' . $name;
    }
}
