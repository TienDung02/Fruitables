<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Factories\DriedProductFactory;
use Database\Factories\FreshProductFactory;
use Database\Factories\JamProductFactory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        (new FreshProductFactory())->withVariants()->count(20)->create();

        (new DriedProductFactory())->withVariants()->count(15)->create();

        (new JamProductFactory())->withVariants()->count(12)->create();

        Product::inRandomOrder()->limit(8)->update(['is_featured' => true]);
    }
}
