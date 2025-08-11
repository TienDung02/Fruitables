<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JamProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        // Berry jams
        $berry = ['blackberry', 'blueberry', 'cranberry', 'mulberry', 'raspberry', 'strawberry'];

        // Citrus jams
        $citrus = ['lemon', 'orange', 'tangerine'];

        // Stone fruit jams
        $stone = ['apricot', 'cherry', 'date', 'peach', 'plum'];

        // Tropical jams
        $tropical = ['banana', 'coconut', 'mango', 'papaya', 'pineapple'];

        $allJams = array_merge($berry, $citrus, $stone, $tropical);
        $name = $this->faker->randomElement($allJams);
        $price = $this->faker->randomFloat(2, 8, 25); // Jam products price range

        // Determine category based on jam type
        $categorySlug = 'berry-jams'; // default
        if (in_array($name, $berry)) {
            $categorySlug = 'berry-jams';
        } elseif (in_array($name, $citrus)) {
            $categorySlug = 'citrus-jams';
        } elseif (in_array($name, $stone)) {
            $categorySlug = 'stone-fruit-jams';
        } elseif (in_array($name, $tropical)) {
            $categorySlug = 'tropical-jams';
        }

        return [
            'name' => ucfirst($name) . ' Jam',
            'slug' => Str::slug($name . '-jam'),
            'description' => "Artisanal {$name} jam made from carefully selected premium fruits. Slow-cooked in small batches to preserve natural flavors and nutrients. Contains real fruit pieces and no artificial preservatives. Perfect for breakfast, desserts, and gourmet cooking.",
            'short_description' => "Premium {$name} jam - artisanal, small batch",
            'price' => $price,
            'sale_price' => $this->faker->boolean(15) ? $price * 0.88 : null,
            'sku' => 'JAM-' . strtoupper($this->faker->bothify('??###')),
            'stock_quantity' => $this->faker->numberBetween(25, 80),
            'category_id' => Category::where('slug', $categorySlug)->first()?->id ?? 1,
            'weight' => $this->faker->randomFloat(2, 0.25, 0.75), // Jam jars weight
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'meta_title' => "{$name} Jam - Artisanal Premium Quality",
            'meta_description' => "Premium {$name} jam made from selected fruits. No preservatives, small batch artisanal.",
        ];
    }

    public function getImagePath($productName): string
    {
        $name = str_replace(['-jam', ' jam'], '', strtolower($productName));

        // Map to correct jam folder structure
        $folderMap = [
            'blackberry' => 'jam/Berry/blackberry',
            'blueberry' => 'jam/Berry/blueberry',
            'cranberry' => 'jam/Berry/cranberry',
            'mulberry' => 'jam/Berry/mulberry',
            'raspberry' => 'jam/Berry/raspberry',
            'strawberry' => 'jam/Berry/strawberry',
            'lemon' => 'jam/Citrus/lemon',
            'orange' => 'jam/Citrus/orange',
            'tangerine' => 'jam/Citrus/tangerine',
            'apricot' => 'jam/Stone/apricot',
            'cherry' => 'jam/Stone/cherry',
            'date' => 'jam/Stone/date',
            'peach' => 'jam/Stone/peach',
            'plum' => 'jam/Stone/plum',
            'banana' => 'jam/Tropical/banana',
            'coconut' => 'jam/Tropical/coconut',
            'mango' => 'jam/Tropical/mango',
            'papaya' => 'jam/Tropical/papaya',
            'pineapple' => 'jam/Tropical/pineapple',
        ];

        return $folderMap[$name] ?? 'jam/Berry/' . $name;
    }
}
