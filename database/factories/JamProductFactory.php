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

        // Determine category based on jam type
        $categorySlug = 'berry-jams'; // default
        if (in_array($name, $berry)) {
            $categorySlug = 'berry-jams';
            $ingredient = ucfirst($name) . " fruit";
            $highlight = "Sweet and delicate flavor, preserves the unique aroma of " . ucfirst($name) . ".";
        } elseif (in_array($name, $citrus)) {
            $categorySlug = 'citrus-jams';
            $ingredient = ucfirst($name) . " fruit";
            $highlight = "Mildly sour, aromatic typical of citrus fruits.";
        } elseif (in_array($name, $stone)) {
            $categorySlug = 'stone-fruit-jams';
            $ingredient = ucfirst($name) . " fruit";
            $highlight = "Mildly sweet, preserves the unique aroma of stone fruits.";
        } elseif (in_array($name, $tropical)) {
            $categorySlug = 'tropical-jams';
            $ingredient = ucfirst($name) . " fruit";
            $highlight = "Tropical flavor, delicious and unique to tropical fruits.";
        } else {
            $ingredient = ucfirst($name) . " fruit";
            $highlight = "Unique flavor of " . ucfirst($name) . ".";
        }

        $description = "Ingredients & Origin\n" .
            "Made from fresh, natural {$ingredient}, no preservatives, sourced from ABC farm in Lam Dong province.\n" .
            "Highlighted Features\n" .
            "{$highlight}\nSmooth texture, easy to spread on bread and pastries.\n" .
            "Benefits\n" .
            "Rich in vitamins, suitable for breakfast, dessert, or as a cooking ingredient.\n" .
            "Packaging\n" .
            "Multiple options: 100g, 200g, 500g, 1kg (each jar size has its own price).\n" .
            "Storage & Shelf Life\n" .
            "Store in a cool, dry place; best kept refrigerated after opening.\n" .
            "Shelf life 6–12 months depending on type.";

        return [
            'name' => ucfirst($name) . ' Jam',
            'slug' => Str::slug($name . '-jam'),
            'description' => $description,
            'short_description' => "Premium {$name} jam - artisanal, small batch",
            'category_id' => Category::where('slug', $categorySlug)->first()?->id ?? 1,
            'is_featured' => $this->faker->boolean(18),
            'is_active' => true,
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
    public function withVariants()
    {
        return $this->afterCreating(function ($product) {
            // Tạo nhiều biến thể cho sản phẩm jam với giá tăng dần theo khối lượng
            $sizes = ['100g', '200g', '500g', '1kg'];
            $basePrices = [500, 800, 1500, 2500]; // Giá tăng theo khối lượng

            foreach ($sizes as $index => $size) {
                $price = $this->faker->numberBetween(
                    $basePrices[$index] + 100,
                    $basePrices[$index] + 300
                );

                // Chỉ tạo sale_price nếu có khoảng hợp lệ
                $salePrice = null;

                if ($price - 1 > $basePrices[$index]) {
                    $salePrice = $this->faker->numberBetween(
                        $basePrices[$index] + 1,
                        $price - 1
                    );
                }
                \App\Models\ProductVariant::factory()->create([
                    'product_id' => $product->id,
                    'unit'       => 'Pack',
                    'size'       => $size,
                    'price'      => $price,
                    'sale_price' => $salePrice,
                ]);
            }

            $product->updatePriceRange();
        });
    }
}
