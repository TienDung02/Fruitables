<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Clear existing categories first
        Category::query()->delete();
        
        // ✅ LEVEL 1: Main Categories (3 main types)
        $mainCategories = [
            [
                'name' => 'Fresh Products',
                'slug' => 'fresh-products',
                'description' => 'Farm-fresh fruits and vegetables harvested at peak ripeness. Naturally grown and packed with vitamins.',
                'image' => 'categories/fresh-products.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Dried Products',
                'slug' => 'dried-products',
                'description' => 'Premium dried fruits with no added sugars or preservatives. Naturally dehydrated for maximum nutrition.',
                'image' => 'categories/dried-products.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Jam Products',
                'slug' => 'jam-products',
                'description' => 'Artisanal jams made from carefully selected premium fruits. Slow-cooked in small batches.',
                'image' => 'categories/jam-products.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        // ✅ LEVEL 2: Subcategories for each main category
        $subCategories = [
            // Fresh Products Subcategories
            'Fresh Products' => [
                [
                    'name' => 'Fresh Fruits',
                    'slug' => 'fresh-fruits',
                    'description' => 'Farm-fresh fruits naturally sweet and packed with vitamins and antioxidants.',
                    'image' => 'categories/fresh-fruits.jpg',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Fresh Vegetables',
                    'slug' => 'fresh-vegetables',
                    'description' => 'Organic vegetables grown using sustainable farming practices.',
                    'image' => 'categories/fresh-vegetables.jpg',
                    'sort_order' => 2,
                ],
            ],
            
            // Dried Products Subcategories
            'Dried Products' => [
                [
                    'name' => 'Dried Berries',
                    'slug' => 'dried-berries',
                    'description' => 'Premium dried berries including blueberries, cranberries, and strawberries.',
                    'image' => 'categories/dried-berries.jpg',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Dried Citrus',
                    'slug' => 'dried-citrus',
                    'description' => 'Naturally dried citrus fruits with intense flavor and aroma.',
                    'image' => 'categories/dried-citrus.jpg',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Dried Stone Fruits',
                    'slug' => 'dried-stone-fruits',
                    'description' => 'Premium dried stone fruits including apricots, dates, and plums.',
                    'image' => 'categories/dried-stone-fruits.jpg',
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Dried Tropical',
                    'slug' => 'dried-tropical',
                    'description' => 'Exotic dried tropical fruits with authentic taste and nutrition.',
                    'image' => 'categories/dried-tropical.jpg',
                    'sort_order' => 4,
                ],
                [
                    'name' => 'Other Dried Fruits',
                    'slug' => 'other-dried-fruits',
                    'description' => 'Various other premium dried fruits including apples, figs, and raisins.',
                    'image' => 'categories/other-dried-fruits.jpg',
                    'sort_order' => 5,
                ],
            ],
            
            // Jam Products Subcategories
            'Jam Products' => [
                [
                    'name' => 'Berry Jams',
                    'slug' => 'berry-jams',
                    'description' => 'Artisanal berry jams made from fresh strawberries, blueberries, and more.',
                    'image' => 'categories/berry-jams.jpg',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Citrus Jams',
                    'slug' => 'citrus-jams',
                    'description' => 'Bright and zesty citrus jams with natural fruit pieces.',
                    'image' => 'categories/citrus-jams.jpg',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Stone Fruit Jams',
                    'slug' => 'stone-fruit-jams',
                    'description' => 'Rich and flavorful jams made from stone fruits like apricots and peaches.',
                    'image' => 'categories/stone-fruit-jams.jpg',
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Tropical Jams',
                    'slug' => 'tropical-jams',
                    'description' => 'Exotic tropical fruit jams with authentic island flavors.',
                    'image' => 'categories/tropical-jams.jpg',
                    'sort_order' => 4,
                ],
            ],
        ];

        // ✅ Step 1: Create Main Categories (Level 1)
        $createdMainCategories = [];
        foreach ($mainCategories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'image' => $categoryData['image'],
                    'is_active' => $categoryData['is_active'],
                    'sort_order' => $categoryData['sort_order'],
                    'parent_id' => null, // ✅ Main categories have no parent
                    'meta_title' => $categoryData['name'] . ' - Premium Quality Products',
                    'meta_description' => $categoryData['description'],
                ]
            );
            
            $createdMainCategories[$categoryData['name']] = $category;
            echo "✅ Created main category: {$category->name}\n";
        }

        // ✅ Step 2: Create Subcategories (Level 2)
        foreach ($subCategories as $parentName => $subs) {
            $parentCategory = $createdMainCategories[$parentName];
            
            foreach ($subs as $subData) {
                $subCategory = Category::updateOrCreate(
                    ['slug' => $subData['slug']],
                    [
                        'name' => $subData['name'],
                        'description' => $subData['description'],
                        'image' => $subData['image'],
                        'is_active' => true,
                        'sort_order' => $subData['sort_order'],
                        'parent_id' => $parentCategory->id, // ✅ Link to parent
                        'meta_title' => $subData['name'] . ' - Premium Quality',
                        'meta_description' => $subData['description'],
                    ]
                );
                
                echo "✅ Created subcategory: {$subCategory->name} (under {$parentName})\n";
            }
        }

        echo "🎉 CategorySeeder completed successfully! Created 3 main categories and their subcategories.\n";
    }
}
