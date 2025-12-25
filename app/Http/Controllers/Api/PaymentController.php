<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SepayPaymentService;
use App\Services\OrderService;
use App\Services\VietQRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $sepayService;
    private $orderService;
    private $vietQRService;

    public function __construct(
        SepayPaymentService $sepayService,
        OrderService $orderService,
        VietQRService $vietQRService
    ) {
        $this->sepayService = $sepayService;
        $this->orderService = $orderService;
        $this->vietQRService = $vietQRService;
    }

    /**
     * Tạo SePay payment với QR code chuẩn VietQR
     */
    public function createSepayPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'shipping_type' => 'required|string|in:free,standard,fast',
                'customer_info' => 'required|array',
                'customer_info.name' => 'required|string',
                'customer_info.emails' => 'required|emails',
                'customer_info.phone' => 'required|string',
                'customer_info.detail_address' => 'required|string',
            ]);

            // Tạo hoặc cập nhật pending order
            if (Auth::check()) {
                $order = $this->orderService->getOrCreatePendingOrder(
                    $validated['items'],
                    $validated['shipping_type'],
                    $validated['customer_info']
                );
            } else {
                Log::info('Creating guest order for SePay');
                $order = $this->orderService->createGuestOrder(
                    $validated['items'],
                    $validated['shipping_type'],
                    $validated['customer_info']
                );
            }

            // Lấy thông tin tài khoản ngân hàng từ config
            $bankAccount = config('payment.bank_account');

            // Tạo VietQR content
            $orderInfo = ($order->id);
            Log::info('oderInfo: ' . $orderInfo);
            $orderCode = (string)($order->id);
            Log::info('$orderCode: ' . $orderInfo);
            $amount = (int) $order->total;

            $qrContent = $this->vietQRService->generateVietQRString(
                $bankAccount['account_number'],
                $bankAccount['bank_code'],
                $bankAccount['account_name'],
                $amount,
                $orderInfo,
                $orderCode
            );

            // Cập nhật order với thông tin payment
            $order->update([
                'payment_method' => 'sepay',
                'payment_status' => Order::PAYMENT_STATUS_PENDING
            ]);

            Log::info('VietQR payment created for order', [
                'order_id' => $order->id,
                'total' => $amount,
                'qr_content_length' => strlen($qrContent)
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $orderCode,
                'qr_content' => $qrContent, // Mã QR chuẩn VietQR
                'qr_formats' => $this->vietQRService->generateMultipleQRFormats($qrContent), // Multiple QR formats
                'amount' => $amount,
                'transfer_content' => $orderInfo,
                'account_number' => $bankAccount['account_number'],
                'bank_info' => $bankAccount
            ]);

        } catch (\Exception $e) {
            Log::error('VietQR Payment Creation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán SePay
     */
    public function checkSepayPaymentStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'orderId' => 'required|string'
            ]);

            // Kiểm tra đơn hàng trước
            $order = Order::find($validated['orderId']);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'Order not found'
                ], 404);
            }

            // ✅ DỪNG nếu đơn hàng đã được thanh toán thành công
            if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                Log::info('🛑 Order already paid, skipping SePay check', [
                    'order_id' => $validated['orderId'],
                    'payment_status' => $order->payment_status,
                    'paid_at' => $order->paid_at
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'message' => 'Order already paid',
                    'transaction_id' => $order->payment_transaction_id,
                    'amount' => $order->total,
                    'already_processed' => true
                ]);
            }

            // ✅ DỪNG nếu đơn hàng đã bị hủy
            if ($order->status === Order::STATUS_CANCELLED) {
                Log::info('🛑 Order is cancelled, skipping SePay check', [
                    'order_id' => $validated['orderId'],
                    'status' => $order->status
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'expired',
                    'message' => 'Order is cancelled'
                ]);
            }

            // Chỉ kiểm tra SePay API nếu đơn hàng vẫn pending
            $result = $this->sepayService->checkTransactionStatus($validated['orderId']);
            Log::info('📡 SePay API result: ' . json_encode($result));

            // ✅ Nếu thanh toán thành công, cập nhật database
            if ($result['status'] === 'paid') {
                $order = Order::find($validated['orderId']);

                if ($order && $order->payment_status !== Order::PAYMENT_STATUS_PAID) {
                    // Cập nhật trạng thái đơn hàng
                    $paymentData = [
                        'payment_method' => 'sepay',
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'payment_data' => $result,
                        'verified_at' => now(),
                        'verified_by' => 'status_check'
                    ];

                    // Sử dụng OrderService để confirm order
                    $this->orderService->confirmOrder($order->id, $paymentData);

                    Log::info('✅ Order payment confirmed via status check', [
                        'order_id' => $validated['orderId'],
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'amount' => $result['amount'] ?? null
                    ]);

                    // Thêm thông báo cho frontend biết đã cập nhật
                    $result['database_updated'] = true;
                    $result['message'] = 'Payment confirmed and order updated';
                }
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('SePay Status Check Error', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('orderId')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Status check failed'
            ], 500);
        }
    }

    /**
     * SePay webhook để xử lý kết quả thanh toán
     */
    public function sepayWebhook(Request $request): JsonResponse
    {
        try {
            Log::info('SePay Webhook Received', $request->all());

            // 1. Verify webhook signature
            $signature = $request->header('X-Sepay-Signature');
            $payload = $request->getContent();

            $expectedSignature = hash_hmac(
                'sha256',
                $payload,
                config('services.sepay.webhook_secret')  // ← Dùng webhook_secret
            );

            if ($signature !== $expectedSignature) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // 2. Lấy dữ liệu giao dịch từ SePay
            $data = $request->all();

            // SePay gửi dữ liệu dạng:
            // {
            //   "id": 123456,
            //   "transaction_date": "2024-01-01 10:00:00",
            //   "account_number": "0123456789",
            //   "amount_in": 100000,
            //   "transaction_content": "DH123456",
            //   "bank_brand_name": "VCB"
            // }

            $transactionContent = strtoupper($data['transaction_content'] ?? '');
            $amountIn = $data['amount_in'] ?? 0;
            $transactionId = $data['id'] ?? null;

            // 3. Extract mã đơn hàng từ nội dung chuyển khoản
            // Ví dụ: "DH123456" hoặc "Thanh toan DH123456" -> lấy DH123456
            preg_match('/DH\d+/', $transactionContent, $matches);
            $orderNumber = $matches[0] ?? null;

            if (!$orderNumber) {
                Log::warning('Cannot extract order number from transaction', [
                    'content' => $transactionContent,
                    'data' => $data
                ]);
                return response()->json(['message' => 'Invalid transaction content'], 400);
            }

            // 4. Tìm đơn hàng theo order_number
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('Order not found', [
                    'order_number' => $orderNumber,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // 5. Kiểm tra đã thanh toán chưa (tránh xử lý trùng)
            if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                Log::info('Order already paid', ['order_number' => $orderNumber]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // 6. Kiểm tra số tiền khớp
            if (abs($amountIn - $order->total_amount) > 0.01) {
                Log::error('Amount mismatch', [
                    'order_number' => $orderNumber,
                    'expected' => $order->total_amount,
                    'received' => $amountIn
                ]);

                // Có thể tạo cảnh báo hoặc order review thủ công
                return response()->json(['message' => 'Amount mismatch'], 400);
            }

            // 7. Cập nhật đơn hàng thành công
            $paymentData = [
                'payment_method' => 'sepay',
                'transaction_id' => $transactionId,
                'payment_data' => $data,
                'verified_at' => now(),
                'verified_by' => 'webhook'
            ];

            $this->orderService->confirmOrder($order->id, $paymentData);

            Log::info('SePay payment confirmed via webhook', [
                'order_number' => $orderNumber,
                'transaction_id' => $transactionId,
                'amount' => $amountIn
            ]);

            return response()->json(['message' => 'Success'], 200);

        } catch (\Exception $e) {
            Log::error('SePay Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json(['message' => 'Internal error'], 500);
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    public function createPayment(Request $request): JsonResponse
    {
        // Redirect to SePay payment creation
        return $this->createSepayPayment($request);
    }

    /**
     * Legacy method for backward compatibility
     */

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus(Request $request, $orderId): JsonResponse
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->update([
                'payment_status' => $request->input('payment_status', 'paid'),
                'status' => $request->input('status', 'confirmed'),
                'payment_method' => $request->input('payment_method', 'sepay'),
                'paid_at' => $request->input('paid_at', now())
            ]);

            Log::info('✅ Order status updated', [
                'order_id' => $orderId,
                'payment_status' => $request->input('payment_status'),
                'status' => $request->input('status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái đơn hàng',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Update order status error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi cập nhật trạng thái đơn hàng'
            ], 500);
        }
    }
}
