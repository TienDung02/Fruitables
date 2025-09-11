<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = \App\Models\ProductVariant::class;
    protected $product;

    public function withProduct($product)
    {
        // Gán đối tượng sản phẩm cho thuộc tính
        $this->product = $product;
        return $this;
    }
    public function definition(): array
    {
        $units = ['kg', 'Jar'];
        $sizes = [null, '100g', '200g', '500g', '1kg'];
        $unit = $this->faker->randomElement($units);
        $size = $this->faker->randomElement($sizes);
        $price = $this->faker->randomFloat(2, 5, 100);
        $salePrice = $this->faker->boolean(30) ? $price * 0.8 : null;
        return [
            'unit' => $unit,
            'size' => $size,
            'price' => $price,
            'sale_price' => $salePrice,
            'stock_quantity' => $this->faker->numberBetween(10, 200),
            'is_active' => true,
        ];
    }
}

