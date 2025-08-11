<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductMediaFactory extends Factory
{
    protected $model = \App\Models\ProductMedia::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'file_path' => 'images/products/default/default_1.jpg',
            'file_name' => 'default_1.jpg',
            'file_size' => $this->faker->numberBetween(100000, 500000),
            'mime_type' => 'image/jpeg',
            'alt_text' => 'Product image',
            'is_primary' => false,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function forProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $slug = $product->slug;

            // Get correct folder path and max images based on product type
            $folderPath = $this->getProductFolder($slug);
            $maxImages = $this->getMaxImages($slug);
            $imageNumber = $this->faker->numberBetween(1, $maxImages);

            // Get base name for image file
            $baseName = $this->getImageBaseName($slug);
            $fileName = $baseName . $imageNumber . '.jpg';

            return [
                'product_id' => $product->id,
                'file_path' => "images/products/{$folderPath}/{$fileName}",
                'file_name' => $fileName,
                'alt_text' => $product->name . ' image',
            ];
        });
    }

    private function getProductFolder($slug): string
    {
        // Dried products
        if (str_contains($slug, 'dried-')) {
            $name = str_replace('dried-', '', $slug);
            $driedMap = [
                // Berries
                'blueberries' => 'dried/Berries/blueberries',
                'cranberries' => 'dried/Berries/cranberries',
                'strawberries' => 'dried/Berries/strawberries',

                // Citrus
                'lemon' => 'dried/Citrus/lemon',
                'orange' => 'dried/Citrus/orange',
                'tangerine' => 'dried/Citrus/tangerine',

                // Others
                'apple' => 'dried/Others/apple',
                'fig' => 'dried/Others/fig',
                'pear' => 'dried/Others/pear',
                'raisins' => 'dried/Others/raisins',

                // Stone
                'apricots' => 'dried/Stone/apricots',
                'cherries' => 'dried/Stone/cherries',
                'dates' => 'dried/Stone/dates',
                'plums' => 'dried/Stone/plums',

                // Tropical
                'banana' => 'dried/Tropical/banana',
                'coconut' => 'dried/Tropical/coconut',
                'mango' => 'dried/Tropical/mango',
                'papaya' => 'dried/Tropical/papaya',
                'pineapple' => 'dried/Tropical/pineapple',
            ];
            return $driedMap[$name] ?? "dried/Others/{$name}";
        }

        // Jam products
        if (str_contains($slug, '-jam')) {
            $name = str_replace('-jam', '', $slug);
            $jamMap = [
                // Berry
                'blackberry' => 'jam/Berry/blackberry',
                'blueberry' => 'jam/Berry/blueberry',
                'cranberry' => 'jam/Berry/cranberry',
                'mulberry' => 'jam/Berry/mulberry',
                'raspberry' => 'jam/Berry/raspberry',
                'strawberry' => 'jam/Berry/strawberry',

                // Citrus
                'lemon' => 'jam/Citrus/lemon',
                'orange' => 'jam/Citrus/orange',
                'tangerine' => 'jam/Citrus/tangerine',

                // Stone
                'apricot' => 'jam/Stone/apricot',
                'cherry' => 'jam/Stone/cherry',
                'date' => 'jam/Stone/date',
                'peach' => 'jam/Stone/peach',
                'plum' => 'jam/Stone/plum',

                // Tropical
                'banana' => 'jam/Tropical/banana',
                'coconut' => 'jam/Tropical/coconut',
                'mango' => 'jam/Tropical/mango',
                'papaya' => 'jam/Tropical/papaya',
                'pineapple' => 'jam/Tropical/pineapple',
            ];
            return $jamMap[$name] ?? "jam/Berry/{$name}";
        }

        // Fresh products
        if (str_contains($slug, 'fresh-')) {
            $name = str_replace('fresh-', '', $slug);
            return "fruit/{$name}";
        }

        // Default fallback
        return "fruit/apple";
    }

    // ✅ NEW: Get max number of images based on product type
    private function getMaxImages($slug): int
    {
        if (str_contains($slug, 'dried-')) {
            return 5; // ✅ Dried products have 5 images
        }

        if (str_contains($slug, '-jam')) {
            return 2; // ✅ Jam products have 2 images
        }

        if (str_contains($slug, 'fresh-')) {
            return 9; // ✅ Fresh products have 9 images
        }

        return 9; // Default fallback
    }

    // ✅ NEW: Get base name for image files
    private function getImageBaseName($slug): string
    {
        if (str_contains($slug, 'dried-')) {
            $name = str_replace('dried-', '', $slug);
            return $name; // e.g., "blueberries" → "blueberries1.jpg"
        }

        if (str_contains($slug, '-jam')) {
            $name = str_replace('-jam', '', $slug);
            return $name; // e.g., "strawberry" → "strawberry1.jpg"
        }

        if (str_contains($slug, 'fresh-')) {
            $name = str_replace('fresh-', '', $slug);
            return $name; // e.g., "apple" → "apple1.jpg"
        }

        return 'default'; // Fallback
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 1,
        ]);
    }

    // ✅ NEW: Create multiple images for a product based on its type
    public function createImagesForProduct(Product $product): void
    {
        $maxImages = $this->getMaxImages($product->slug);

        // Create primary image (image 1)
        $this->forProduct($product)->primary()->create();

        // Create additional images (2 to maxImages)
        if ($maxImages > 1) {
            $additionalImages = $this->faker->numberBetween(
                min(2, $maxImages),
                $maxImages
            );

            for ($i = 2; $i <= $additionalImages; $i++) {
                $this->forProduct($product)->create([
                    'sort_order' => $i,
                    'is_primary' => false
                ]);
            }
        }
    }
}
