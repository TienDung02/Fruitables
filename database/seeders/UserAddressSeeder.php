<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
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

        foreach ($users as $user) {
            // Create 1-3 addresses for each user
            $addressCount = rand(1, 3);

            for ($i = 0; $i < $addressCount; $i++) {
                $isDefault = $i === 0; // First address is default

                UserAddress::factory()->create([
                    'user_id' => $user->id,
                    'first_name' => $user->name ? explode(' ', $user->name)[0] : fake()->firstName(),
                    'last_name' => $user->name ? (explode(' ', $user->name)[1] ?? fake()->lastName()) : fake()->lastName(),
                    'email' => $user->email,
                    'is_default' => $isDefault,
                ]);
            }
        }

        // Create some standalone addresses for testing
        UserAddress::factory(10)->create();
    }
}
