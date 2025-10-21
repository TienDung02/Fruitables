<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class UserNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        foreach ($users as $user) {
            // Create 3-8 notifications for each user
            $notificationCount = rand(3, 8);

            // Create different types of notifications
            for ($i = 0; $i < $notificationCount; $i++) {
                $type = fake()->randomElement(['order', 'voucher', 'system', 'promotion']);

                UserNotification::factory()
                    ->state(['type' => $type])
                    ->create([
                        'user_id' => $user->id,
                        'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                    ]);
            }

            // Ensure each user has at least one of each notification type
            $types = ['order', 'voucher', 'system', 'promotion'];
            foreach ($types as $type) {
                UserNotification::factory()
                    ->state(['type' => $type])
                    ->create([
                        'user_id' => $user->id,
                        'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
                    ]);
            }
        }

        // Create some recent system notifications for all users
        $systemNotifications = [
            [
                'title' => 'Thông báo bảo trì hệ thống',
                'message' => 'Hệ thống sẽ bảo trì vào 02:00 - 04:00 ngày mai. Cảm ơn sự thông cảm của quý khách.',
                'type' => 'system'
            ],
            [
                'title' => 'Cập nhật chính sách giao hàng',
                'message' => 'Chúng tôi đã cập nhật chính sách giao hàng mới. Thời gian giao hàng nhanh hơn và uy tín hơn.',
                'type' => 'system'
            ],
            [
                'title' => 'Khuyến mãi tháng 10',
                'message' => 'Giảm giá lên đến 50% cho tất cả sản phẩm trái cây tươi trong tháng 10. Đừng bỏ lỡ!',
                'type' => 'promotion'
            ]
        ];

        foreach ($users as $user) {
            foreach ($systemNotifications as $notification) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'type' => $notification['type'],
                    'is_read' => fake()->boolean(20), // 20% chance already read
                    'created_at' => fake()->dateTimeBetween('-3 days', 'now'),
                ]);
            }
        }

        $this->command->info('UserNotification seeder completed successfully.');
    }
}
