<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = $this->faker->numberBetween(1, 5);
        
        // Generate comments based on rating to make it more realistic
        $comments = [
            1 => [
                'Sản phẩm không đúng như mô tả, chất lượng kém.',
                'Rất thất vọng với chất lượng sản phẩm này.',
                'Không khuyến khích mua sản phẩm này.',
                'Chất lượng không tương xứng với giá tiền.'
            ],
            2 => [
                'Sản phẩm tạm được nhưng có một số vấn đề.',
                'Giá hơi cao so với chất lượng.',
                'Cần cải thiện thêm về chất lượng.',
                'Có thể tốt hơn ở mức giá này.'
            ],
            3 => [
                'Sản phẩm ổn, đáp ứng được nhu cầu cơ bản.',
                'Chất lượng trung bình, giá cả hợp lý.',
                'Không quá xuất sắc nhưng cũng không tệ.',
                'Tạm được cho nhu cầu sử dụng hàng ngày.'
            ],
            4 => [
                'Sản phẩm tốt, chất lượng khá ổn.',
                'Đáng tiền, sẽ mua lại lần sau.',
                'Chất lượng tốt, giao hàng nhanh.',
                'Sản phẩm như mong đợi, hài lòng.'
            ],
            5 => [
                'Sản phẩm xuất sắc! Vượt ngoài mong đợi.',
                'Chất lượng tuyệt vời, rất hài lòng.',
                'Sẽ giới thiệu cho bạn bè và người thân.',
                'Sản phẩm tốt nhất trong phân khúc.',
                'Hoàn hảo! Chính xác những gì tôi cần.'
            ]
        ];

        return [
            'user_id' => \App\Models\User::factory(),
            'rating' => $rating,
            'comment' => $this->faker->randomElement($comments[$rating]),
            'is_approved' => $this->faker->boolean(85), // 85% chance of being approved
            'reviewed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
