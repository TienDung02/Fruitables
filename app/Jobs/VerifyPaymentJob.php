<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Services\PaymentVerificationService;
use Illuminate\Support\Facades\Log;

class VerifyPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $retryCount = 0;
    protected $maxRetries = 10; // Thử lại tối đa 10 lần trong 30 phút

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, int $retryCount = 0)
    {
        $this->order = $order;
        $this->retryCount = $retryCount;

        // Delay progressively: 1 phút, 2 phút, 3 phút, ...
        $this->delay = now()->addMinutes($retryCount + 1);
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentVerificationService $paymentVerificationService): void
    {
        try {
            Log::info("Checking payment for order: {$this->order->order_number} (attempt {$this->retryCount})");

            // Kiểm tra nếu đơn hàng đã được thanh toán
            $this->order->refresh();
            if ($this->order->payment_status === 'paid') {
                Log::info("Order {$this->order->order_number} already paid, stopping verification");
                return;
            }

            // Tự động xác minh thanh toán
            $result = $paymentVerificationService->autoVerifyPayment($this->order);

            if ($result['status'] === 'paid') {
                Log::info("Payment verified for order: {$this->order->order_number}");

                // Có thể thêm notification cho user ở đây
                // event(new PaymentVerifiedEvent($this->order));

            } elseif ($result['status'] === 'pending' && $this->retryCount < $this->maxRetries) {
                // Chưa thanh toán, thử lại sau
                Log::info("Payment still pending for order: {$this->order->order_number}, scheduling retry");
                VerifyPaymentJob::dispatch($this->order, $this->retryCount + 1);

            } elseif ($result['status'] === 'error') {
                Log::error("Error verifying payment for order: {$this->order->order_number} - {$result['message']}");

                // Thử lại nếu chưa đến giới hạn
                if ($this->retryCount < $this->maxRetries) {
                    VerifyPaymentJob::dispatch($this->order, $this->retryCount + 1);
                }
            }

        } catch (\Exception $e) {
            Log::error("VerifyPaymentJob failed for order {$this->order->order_number}: " . $e->getMessage(), [
                'retry_count' => $this->retryCount,
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Thử lại nếu chưa đến giới hạn
            if ($this->retryCount < $this->maxRetries) {
                VerifyPaymentJob::dispatch($this->order, $this->retryCount + 1);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("VerifyPaymentJob ultimately failed for order {$this->order->order_number}: " . $exception->getMessage());
    }
}
