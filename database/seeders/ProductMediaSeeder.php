<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductMedia;
use Database\Factories\ProductMediaFactory;
use Illuminate\Database\Seeder;

class ProductMediaSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $mediaFactory = new ProductMediaFactory();

        foreach ($products as $product) {
            echo "Creating images for: {$product->name}\n";

            // ✅ Use new method that creates appropriate number of images
            $mediaFactory->createImagesForProduct($product);
        }

        echo "✅ Created media for " . $products->count() . " products\n";
    }
}
