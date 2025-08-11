<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả products
        $products = Product::all();

        foreach ($products as $product) {
            // Mỗi sản phẩm sẽ có 3-8 reviews
            $reviewCount = rand(3, 8);
            
            Review::factory()->count($reviewCount)->create([
                'product_id' => $product->id,
            ]);
        }

        $this->command->info('Created reviews for all products.');
    }
}
