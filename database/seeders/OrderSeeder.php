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
            User::factory(50)->create();
        }

        if ($variantCount === 0) {
            $this->command->warn('No product variants found. Please run ProductVariantSeeder first.');
            return;
        }

        $this->command->info('Creating orders and order items...');

        // Tạo orders với trạng thái và phương thức thanh toán khác nhau
        $orders = collect();

        // Tạo 150 orders đã hoàn thành với SePay (để có dữ liệu bestsellers)
        $completedSepayOrders = Order::factory(150)
            ->completed()
            ->sepayPayment()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($completedSepayOrders);

        // Tạo 50 orders đã hoàn thành với COD
        $completedCodOrders = Order::factory(50)
            ->state([
                'status' => Order::STATUS_DELIVERED,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'payment_data' => ['payment_method' => 'cod'],
                'paid_at' => now()->subDays(rand(1, 30)),
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($completedCodOrders);

        // Tạo 100 orders đang xử lý với SePay
        $processingSepayOrders = Order::factory(100)
            ->state([
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'payment_data' => [
                    'payment_method' => 'sepay',
                    'transaction_ref' => fake()->uuid(),
                    'bank_code' => '970423'
                ],
                'paid_at' => now()->subDays(rand(1, 10)),
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($processingSepayOrders);

        // Tạo 80 orders đã xác nhận
        $confirmedOrders = Order::factory(80)
            ->state([
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'paid_at' => now()->subDays(rand(1, 5)),
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($confirmedOrders);

        // Tạo 60 orders đang chờ xử lý (COD)
        $pendingCodOrders = Order::factory(60)
            ->pending()
            ->codPayment()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($pendingCodOrders);

        // Tạo 20 orders đang chờ xử lý (SePay chưa thanh toán)
        $pendingSepayOrders = Order::factory(20)
            ->state([
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'payment_data' => ['payment_method' => 'sepay'],
                'paid_at' => null,
            ])
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($pendingSepayOrders);

        // Tạo 30 orders bị hủy
        $cancelledOrders = Order::factory(30)
            ->cancelled()
            ->forExistingUser()
            ->create();
        $orders = $orders->merge($cancelledOrders);

        // Tạo 20 orders cho guest checkout
        $guestOrders = Order::factory(20)
            ->guest()
            ->state([
                'status' => fake()->randomElement([
                    Order::STATUS_PENDING,
                    Order::STATUS_CONFIRMED,
                    Order::STATUS_DELIVERED
                ]),
                'payment_status' => fake()->randomElement([
                    Order::PAYMENT_STATUS_PENDING,
                    Order::PAYMENT_STATUS_PAID
                ]),
            ])
            ->create();
        $orders = $orders->merge($guestOrders);

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
                        'productVariant_id' => $variant->id,
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

        $this->command->info('Order Status:');
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

        // Thống kê theo phương thức thanh toán
        $paymentMethodStats = DB::table('orders')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(payment_data, '$.payment_method')) as payment_method"),
                DB::raw('count(*) as count')
            )
            ->whereNotNull('payment_data')
            ->groupBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(payment_data, '$.payment_method'))"))
            ->get();

        $this->command->info('Payment Methods:');
        foreach ($paymentMethodStats as $stat) {
            $this->command->line("- {$stat->payment_method}: {$stat->count} orders");
        }

        // Thống kê user vs guest orders
        $userOrderCount = DB::table('orders')->whereNotNull('user_id')->count();
        $guestOrderCount = DB::table('orders')->whereNull('user_id')->count();

        $this->command->info('User Types:');
        $this->command->line("- Registered users: {$userOrderCount} orders");
        $this->command->line("- Guest checkout: {$guestOrderCount} orders");

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
