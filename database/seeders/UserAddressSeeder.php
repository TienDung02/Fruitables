<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing users
        $users = User::all();

        // Get some random wards for addresses
        $wards = Ward::inRandomOrder()->limit(50)->get();

        if ($wards->isEmpty()) {
            $this->command->warn('No wards found. Please run ProvinceSeeder, DistrictSeeder, and WardSeeder first.');
            return;
        }

        foreach ($users as $user) {
            // Create 1-3 addresses for each user
            $addressCount = rand(1, 3);

            for ($i = 0; $i < $addressCount; $i++) {
                $isDefault = $i === 0; // First address is default
                $labels = ['home', 'work', 'other'];
                $label = $i === 0 ? 'home' : $labels[array_rand($labels)];

                UserAddress::factory()->create([
                    'user_id' => $user->id,
                    'name' => $user->full_name ?? fake()->name(),
                    'phone' => $user->phone ?? fake()->phoneNumber(),
                    'ward_id' => $wards->random()->id,
                    'label' => $label,
                    'is_default' => $isDefault,
                ]);
            }
        }

        $this->command->info('UserAddress seeder completed successfully.');
    }
}
