<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired {--hours=24 : Number of hours after which orders are considered expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending orders that have been abandoned for too long';

    private $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');

        $this->info("Checking for pending orders older than {$hours} hours...");

        try {
            $cancelledCount = $this->orderService->cancelExpiredPendingOrders($hours);

            if ($cancelledCount > 0) {
                $this->info("Successfully cancelled {$cancelledCount} expired pending orders.");
            } else {
                $this->info("No expired pending orders found.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error cancelling expired orders: " . $e->getMessage());
            return 1;
        }
    }
}
