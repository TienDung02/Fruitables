<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserNotification>
 */
class UserNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['order', 'voucher', 'system', 'promotion']);

        $titles = [
            'order' => [
                'Đơn hàng của bạn đã được xác nhận',
                'Đơn hàng đang được giao',
                'Đơn hàng giao thành công',
                'Đơn hàng đã bị hủy',
                'Đơn hàng được cập nhật'
            ],
            'voucher' => [
                'Bạn có voucher mới',
                'Voucher sắp hết hạn',
                'Voucher đã được sử dụng',
                'Chúc mừng! Bạn nhận được voucher'
            ],
            'system' => [
                'Thông báo bảo trì hệ thống',
                'Cập nhật điều khoản sử dụng',
                'Thông báo từ hệ thống',
                'Cập nhật ứng dụng mới'
            ],
            'promotion' => [
                'Khuyến mãi đặc biệt cho bạn',
                'Flash Sale đang diễn ra',
                'Giảm giá cuối tuần',
                'Chương trình khuyến mãi mới'
            ]
        ];

        $messages = [
            'order' => [
                'Đơn hàng #' . fake()->numberBetween(1000, 9999) . ' đã được xác nhận và đang được chuẩn bị.',
                'Đơn hàng của bạn đang trên đường giao. Dự kiến giao trong 2-3 ngày.',
                'Đơn hàng đã được giao thành công. Cảm ơn bạn đã mua hàng!',
                'Đơn hàng đã bị hủy theo yêu cầu của bạn.',
                'Thông tin đơn hàng đã được cập nhật. Vui lòng kiểm tra lại.'
            ],
            'voucher' => [
                'Bạn nhận được voucher giảm ' . fake()->numberBetween(10, 50) . '% cho đơn hàng tiếp theo.',
                'Voucher của bạn sẽ hết hạn vào ' . fake()->dateTimeBetween('+1 day', '+7 days')->format('d/m/Y') . '. Hãy sử dụng ngay!',
                'Voucher đã được áp dụng thành công cho đơn hàng của bạn.',
                'Chúc mừng! Bạn nhận được voucher miễn phí ship.'
            ],
            'system' => [
                'Hệ thống sẽ bảo trì vào ' . fake()->dateTimeBetween('+1 day', '+7 days')->format('d/m/Y H:i') . '. Cảm ơn sự thông cảm.',
                'Điều khoản sử dụng đã được cập nhật. Vui lòng đọc kỹ thông tin mới.',
                'Chúng tôi có một số cập nhật quan trọng cho bạn.',
                'Phiên bản ứng dụng mới đã có sẵn. Hãy cập nhật ngay!'
            ],
            'promotion' => [
                'Giảm giá lên đến ' . fake()->numberBetween(20, 70) . '% cho tất cả sản phẩm trái cây tươi.',
                'Flash Sale 12:00 - 14:00 hôm nay. Giảm giá cực sốc!',
                'Cuối tuần vui vẻ với ưu đãi giảm giá ' . fake()->numberBetween(15, 40) . '%.',
                'Chương trình "Mua 2 tặng 1" đang diễn ra. Đừng bỏ lỡ!'
            ]
        ];

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement($titles[$type]),
            'message' => fake()->randomElement($messages[$type]),
            'type' => $type,
            'is_read' => fake()->boolean(30), // 30% chance đã đọc
        ];
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Create order notification.
     */
    public function order(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'order',
        ]);
    }

    /**
     * Create voucher notification.
     */
    public function voucher(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'voucher',
        ]);
    }

    /**
     * Create system notification.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'system',
        ]);
    }

    /**
     * Create promotion notification.
     */
    public function promotion(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'promotion',
        ]);
    }
}
