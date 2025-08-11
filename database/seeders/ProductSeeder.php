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
        // ✅ ĐÚNG: Gọi trực tiếp FreshProductFactory
        (new FreshProductFactory())->count(20)->create();

        // ✅ ĐÚNG: Gọi trực tiếp DriedProductFactory  
        (new DriedProductFactory())->count(15)->create();

        // ✅ ĐÚNG: Gọi trực tiếp JamProductFactory
        (new JamProductFactory())->count(12)->create();

        // ✅ Set some as featured randomly
        Product::inRandomOrder()->limit(8)->update(['is_featured' => true]);
    }
}