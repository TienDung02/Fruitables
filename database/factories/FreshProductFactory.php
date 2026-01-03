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
        $vegetables = ['broccoli', 'carrot', 'cucumber', 'tomato'];
        $isVegetable = in_array($name, $vegetables);

        // Origin & Quality
        $origin = "Sourced from ABC farm in Lam Dong province, carefully selected from reputable growing regions. Guaranteed fresh, safe, and free from harmful chemicals.";

        // Highlighted Features
        if ($isVegetable) {
            $features = "Natural color, crisp texture, mild flavor. Rich in vitamins and minerals, great for health.";
            $benefits = "Provides daily nutrition and energy. Ideal for direct consumption, salads, or cooking.";
        } else {
            // Fruit
            $features = "Natural color, sweet or mildly tart flavor depending on variety. Rich in vitamins, minerals, and antioxidants.";
            $benefits = "Boosts daily energy and nutrition. Perfect for eating fresh, making juice, salads, or cooking.";
        }

        // Selling Method
        $selling = "Sold by kilogram, customers can choose the quantity they need.";

        // Storage & Shelf Life
        $storage = "Store in a cool, ventilated place or in the refrigerator. Best consumed within a few days of delivery for optimal freshness.";

        $description = "Origin & Quality\n" .
            "$origin\n" .
            "Highlighted Features\n" .
            "$features\n" .
            "Benefits\n" .
            "$benefits\n" .
            "Selling Method\n" .
            "$selling\n" .
            "Storage & Shelf Life\n" .
            "$storage";

        return [
            'name' => 'Fresh ' . ucfirst($name),
            'slug' => Str::slug('fresh-' . $name),
            'description' => $description,
            'short_description' => "Farm-fresh {$name} - naturally grown, peak quality",
            'category_id' => Category::where('slug', $isVegetable ? 'fresh-vegetables' : 'fresh-fruits')->first()?->id ?? 1,
            'is_featured' => $this->faker->boolean(30),
            'is_active' => true,
        ];
    }

    public function getImagePath($productName): string
    {
        $name = str_replace(['fresh-', 'fresh '], '', strtolower($productName));
        return "fruit/{$name}";
    }

    public function withVariants()
    {
        return $this->afterCreating(function ($product) {
            // Tạo một variant duy nhất cho sản phẩm fresh với đơn vị kg
            // Giá fresh products từ 500-8000 VND/kg
            $price = $this->faker->numberBetween(500, 8000);


            $variant = \App\Models\ProductVariant::factory()->create([
                'product_id' => $product->id,
                'unit' => 'kg',
                'size' => '1kg',
                'price' => $price
            ]);

            // Đối với fresh products, min_price = max_price vì chỉ có 1 variant
            $product->update([
                'min_price' => $variant->price,
                'max_price' => $variant->price,
            ]);
        });
    }
}
