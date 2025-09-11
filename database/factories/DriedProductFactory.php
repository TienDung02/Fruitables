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
            $ingredient = "Made from fresh, carefully selected " . rtrim($name, 's') . " berries from ABC farm in Lam Dong province.";
            $feature = "Chewy or crispy texture, mildly sweet, easy to eat. No preservatives, safe for health.";
            $benefit = "Rich in fiber, vitamins, and minerals. A healthy snack alternative to regular snacks.";
        } elseif (in_array($name, $citrus)) {
            $categorySlug = 'dried-citrus';
            $ingredient = "Made from fresh, carefully selected " . $name . " from ABC farm in Lam Dong province.";
            $feature = "Chewy or crispy texture, mildly sweet and tangy, easy to eat. No preservatives, safe for health.";
            $benefit = "Provides fiber, vitamin C, and minerals. Great for snacking and baking.";
        } elseif (in_array($name, $stone)) {
            $categorySlug = 'dried-stone-fruits';
            $ingredient = "Made from fresh, carefully selected " . rtrim($name, 's') . " stone fruits from ABC farm in Lam Dong province.";
            $feature = "Chewy or crispy texture, mildly sweet, easy to eat. No preservatives, safe for health.";
            $benefit = "High in fiber, vitamins, and minerals. A healthy snack for all ages.";
        } elseif (in_array($name, $tropical)) {
            $categorySlug = 'dried-tropical';
            $ingredient = "Made from fresh, carefully selected " . $name . " from ABC farm in Lam Dong province.";
            $feature = "Chewy or crispy texture, tropical flavor, mildly sweet, easy to eat. No preservatives, safe for health.";
            $benefit = "Rich in fiber, vitamins, and minerals. A healthy snack alternative to regular snacks.";
        } else {
            $ingredient = "Made from fresh, carefully selected " . $name . " from ABC farm in Lam Dong province.";
            $feature = "Chewy or crispy texture, mildly sweet, easy to eat. No preservatives, safe for health.";
            $benefit = "Rich in fiber, vitamins, and minerals. A healthy snack alternative to regular snacks.";
        }

        $packaging = "Available in bags/boxes: 100g, 200g, 500g, 1kg (each size has its own price).";
        $storage = "Store in a cool, dry place. Seal tightly after opening. Shelf life 6–12 months.";

        $description = "Ingredients & Origin\n" .
            "$ingredient\n" .
            "Highlighted Features\n" .
            "$feature\n" .
            "Benefits\n" .
            "$benefit\n" .
            "Packaging\n" .
            "$packaging\n" .
            "Storage & Shelf Life\n" .
            "$storage";

        return [
            'name' => 'Dried ' . ucfirst($name),
            'slug' => Str::slug('dried-' . $name),
            'description' => $description,
            'short_description' => "Natural dried {$name} - no additives, pure nutrition",
            'category_id' => Category::where('slug', $categorySlug)->first()?->id ?? 1,
            'is_featured' => $this->faker->boolean(25),
            'is_active' => true,
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
    public function withVariants()
    {
        return $this->afterCreating(function ($product) {
            // Tạo nhiều biến thể cho sản phẩm dried
            $sizes = ['100g', '200g', '500g', '1kg'];
            foreach ($sizes as $size) {
                \App\Models\ProductVariant::factory()->create([
                    'product_id' => $product->id,
                    'unit' => 'Pack',
                    'size' => $size,
                ]);
            }
        });
    }
}
