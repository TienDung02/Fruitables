<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo có users và product variants
        $userCount = User::count();
        $variantCount = ProductVariant::count();

        if ($userCount === 0) {
            $this->command->warn('No users found. Creating some users first...');
            User::factory(10)->create();
        }

        if ($variantCount === 0) {
            $this->command->warn('No product variants found. Please run ProductVariantSeeder first.');
            return;
        }

        $this->command->info('Creating orders and order items...');

        // Tạo 50 orders với trạng thái khác nhau
        $orders = collect();

        // Tạo 20 orders đã hoàn thành (để có dữ liệu bestsellers)
        $completedOrders = Order::factory(20)
            ->completed()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($completedOrders);

        // Tạo 15 orders đang xử lý
        $processingOrders = Order::factory(15)
            ->state([
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'paid_at' => now()->subDays(rand(1, 10)),
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($processingOrders);

        // Tạo 8 orders đã xác nhận
        $confirmedOrders = Order::factory(8)
            ->state([
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'paid_at' => now()->subDays(rand(1, 5)),
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($confirmedOrders);

        // Tạo 7 orders đang chờ xử lý
        $pendingOrders = Order::factory(7)
            ->pending()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($pendingOrders);

        // Tạo 5 orders bị hủy
        $cancelledOrders = Order::factory(5)
            ->cancelled()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($cancelledOrders);

        $this->command->info('Created ' . $orders->count() . ' orders');

        // Tạo order items cho mỗi order
        $totalOrderItems = 0;

        DB::transaction(function () use ($orders, &$totalOrderItems) {
            foreach ($orders as $order) {
                // Mỗi order có từ 1-5 items
                $itemCount = rand(1, 5);
                $orderSubtotal = 0;

                // Lấy các product variants ngẫu nhiên cho order này
                $selectedVariants = ProductVariant::inRandomOrder()
                    ->limit($itemCount)
                    ->get();

                foreach ($selectedVariants as $variant) {
                    $quantity = rand(1, 3);
                    $price = $variant->sale_price ?? $variant->price;

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $orderSubtotal += ($price * $quantity);
                    $totalOrderItems++;
                }

                // Cập nhật lại subtotal và total của order
                $order->update([
                    'subtotal' => $orderSubtotal,
                    'total' => $orderSubtotal + $order->shipping_cost,
                ]);
            }
        });

        $this->command->info('Created ' . $totalOrderItems . ' order items');

        // Hiển thị thống kê
        $this->displayStatistics();
    }

    /**
     * Display seeding statistics.
     */
    private function displayStatistics(): void
    {
        $this->command->info('=== Order Seeding Statistics ===');

        $statusStats = DB::table('orders')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($statusStats as $stat) {
            $this->command->line("- {$stat->status}: {$stat->count} orders");
        }

        $paymentStats = DB::table('orders')
            ->select('payment_status', DB::raw('count(*) as count'))
            ->groupBy('payment_status')
            ->get();

        $this->command->info('Payment Status:');
        foreach ($paymentStats as $stat) {
            $this->command->line("- {$stat->payment_status}: {$stat->count} orders");
        }

        $totalRevenue = DB::table('orders')
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('total');

        $this->command->info("Total Revenue (Paid Orders): $" . number_format($totalRevenue, 2));

        $avgOrderValue = DB::table('orders')
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->avg('total');

        $this->command->info("Average Order Value: $" . number_format($avgOrderValue, 2));
    }
}
