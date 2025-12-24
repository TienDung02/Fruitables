<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users first
        User::factory(10)->create();

        $this->call([
            // Location data (provinces, districts, wards)
            ProvinceSeeder::class,
            DistrictSeeder::class,
            WardSeeder::class,
            CurrencySeeder::class,

            // Product related
            CategorySeeder::class,
            ProductSeeder::class,
            ProductMediaSeeder::class,
            ReviewSeeder::class,

            // User related (depends on users and wards)
            UserAddressSeeder::class,
            UserNotificationSeeder::class,

            // Order related (depends on users, products)
            OrderSeeder::class,
        ]);
    }
}
