<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Fresh Fruits', 'Fresh Vegetables', 'Dried Fruits', 'Jams & Preserves',
            'Organic Products', 'Premium Selection', 'Seasonal Specials'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(15),
            'image' => 'categories/' . Str::slug($name) . '.jpg',
            'parent_id' => null, // ✅ Default no parent
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10), // ✅ Random sort order
            'meta_title' => $name . ' - Premium Quality Products', // ✅ SEO title
            'meta_description' => $this->faker->sentence(20), // ✅ SEO description
        ];
    }

    // ✅ State for subcategories
    public function subcategory(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => \App\Models\Category::factory(),
                'sort_order' => $this->faker->numberBetween(1, 5),
            ];
        });
    }

    // ✅ State for main categories (no parent)
    public function mainCategory(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
                'sort_order' => $this->faker->numberBetween(1, 10),
            ];
        });
    }
}
