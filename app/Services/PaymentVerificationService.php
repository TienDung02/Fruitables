<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentVerificationService
{
    /**
     * Kiểm tra giao dịch ngân hàng thông qua SePay API thực tế
     */
    public function checkBankTransaction($order)
    {
        try {
            $secretKey = config('payment.sepay.secret_key');
            $accountNumber = config('payment.sepay.account_number');

            if (!$secretKey || !$accountNumber) {
                Log::warning('SePay configuration not complete', [
                    'has_secret_key' => !empty($secretKey),
                    'has_account_number' => !empty($accountNumber)
                ]);
                return ['status' => 'unknown', 'message' => 'SePay configuration incomplete'];
            }

            Log::info('Checking bank transaction for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total ?? $order->total_amount,
                'account_number' => $accountNumber
            ]);

            // Gọi SePay API để lấy lịch sử giao dịch
            $transactions = $this->getSepayTransactionHistory($accountNumber, $secretKey);

            if (!$transactions || !isset($transactions['data'])) {
                return [
                    'status' => 'pending',
                    'message' => 'Unable to fetch transaction history'
                ];
            }

            // Tìm giao dịch khớp với đơn hàng
            $orderAmount = (int) ($order->total ?? $order->total_amount);
            $orderReference = $order->order_number ?? $order->id;

            foreach ($transactions['data'] as $transaction) {
                $transactionContent = strtoupper($transaction['transaction_content'] ?? '');
                $transactionAmount = (int) ($transaction['amount_in'] ?? 0);
                $transactionDate = $transaction['transaction_date'] ?? null;

                // Kiểm tra điều kiện khớp
                if ($this->isMatchingTransaction($transaction, $orderReference, $orderAmount)) {
                    // Tìm thấy giao dịch khớp
                    Log::info('Found matching transaction', [
                        'order_id' => $order->id,
                        'transaction_id' => $transaction['id'] ?? null,
                        'amount' => $transactionAmount,
                        'content' => $transactionContent
                    ]);

                    return [
                        'status' => 'paid',
                        'transaction_id' => $transaction['id'] ?? null,
                        'amount' => $transactionAmount,
                        'transaction_date' => $transactionDate,
                        'content' => $transactionContent,
                        'verified_at' => now()
                    ];
                }
            }

            // Không tìm thấy giao dịch khớp
            return [
                'status' => 'pending',
                'message' => 'No matching transaction found'
            ];

        } catch (\Exception $e) {
            Log::error('Bank transaction check error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to check bank transaction: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy lịch sử giao dịch từ SePay API
     */
    private function getSepayTransactionHistory($accountNumber, $secretKey)
    {
        try {
            // Tạo request đến SePay API
            $url = 'https://my.sepay.vn/userapi/transactions/list';

            // Tham số request
            $params = [
                'account_number' => $accountNumber,
                'limit' => 50,
                'from_date' => now()->subDays(7)->format('Y-m-d'), // 7 ngày trước
                'to_date' => now()->format('Y-m-d')
            ];

            // Tạo signature cho API call
            $signature = $this->createSepaySignature($params, $secretKey);
            $params['signature'] = $signature;

            $response = Http::timeout(30)->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SePay API response', [
                    'status' => $response->status(),
                    'transaction_count' => count($data['transactions'] ?? [])
                ]);
                return $data;
            } else {
                Log::warning('SePay API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('SePay API call failed', [
                'error' => $e->getMessage(),
                'account_number' => $accountNumber
            ]);
            return null;
        }
    }

    /**
     * Kiểm tra xem giao dịch có khớp với đơn hàng không
     */
    private function isMatchingTransaction($transaction, $orderReference, $orderAmount)
    {
        $transactionContent = strtoupper($transaction['transaction_content'] ?? '');
        $transactionAmount = (int) ($transaction['amount_in'] ?? 0);
        $orderReferenceUpper = strtoupper($orderReference);

        // Kiểm tra điều kiện khớp:
        // 1. Số tiền phải khớp chính xác
        // 2. Nội dung chuyển khoản phải chứa mã đơn hàng
        // 3. Là giao dịch vào (amount_in > 0)
        return $transactionAmount >= $orderAmount &&
               $transactionAmount > 0 &&
               (strpos($transactionContent, $orderReferenceUpper) !== false ||
                strpos($transactionContent, str_replace(['ORD-', 'ORD_'], '', $orderReferenceUpper)) !== false);
    }

    /**
     * Tạo signature cho SePay API
     */
    private function createSepaySignature($params, $secretKey)
    {
        // Sắp xếp params theo alphabet
        ksort($params);

        // Tạo query string
        $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        // Tạo signature bằng HMAC SHA256
        return hash_hmac('sha256', $queryString, $secretKey);
    }

    /**
     * Xác minh QR Code và nội dung chuyển khoản
     */
    public function verifyQRContent($qrContent, $order)
    {
        try {
            // Phân tích QR code VietQR
            $qrData = $this->parseVietQRCode($qrContent);

            if (!$qrData) {
                return ['valid' => false, 'message' => 'Invalid QR code format'];
            }

            // Kiểm tra số tài khoản
            $expectedAccount = config('payment.bank_account.account_number');
            if ($qrData['account_number'] !== $expectedAccount) {
                return ['valid' => false, 'message' => 'Account number mismatch'];
            }

            // Kiểm tra số tiền
            if ($qrData['amount'] != $order->total_amount) {
                return ['valid' => false, 'message' => 'Amount mismatch'];
            }

            // Kiểm tra nội dung chuyển khoản
            $content = strtoupper($qrData['content'] ?? '');
            $orderNumber = strtoupper($order->order_number);

            if (strpos($content, $orderNumber) === false) {
                return ['valid' => false, 'message' => 'Order number not found in content'];
            }

            return [
                'valid' => true,
                'account_number' => $qrData['account_number'],
                'amount' => $qrData['amount'],
                'content' => $qrData['content']
            ];

        } catch (\Exception $e) {
            Log::error('QR verification error: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'QR verification failed'];
        }
    }

    /**
     * Phân tích QR code VietQR
     */
    private function parseVietQRCode($qrContent)
    {
        try {
            // Parse QR code theo chuẩn VietQR
            $data = [];

            // Tìm account number (tag 38)
            if (preg_match('/38\d{2}0010A000000727012\d{2}(\d{6})011\d{2}(\d+)/', $qrContent, $matches)) {
                $data['bank_code'] = $matches[1];
                $data['account_number'] = $matches[2];
            }

            // Tìm amount (tag 54)
            if (preg_match('/54\d{2}(\d+)/', $qrContent, $matches)) {
                $data['amount'] = intval($matches[1]);
            }

            // Tìm merchant name (tag 59)
            if (preg_match('/59\d{2}(.+?)60/', $qrContent, $matches)) {
                $data['merchant_name'] = $matches[1];
            }

            // Tìm nội dung chuyển khoản (tag 62)
            if (preg_match('/62\d{2}.*?08\d{2}(.+?)63/', $qrContent, $matches)) {
                $data['content'] = $matches[1];
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('QR parsing error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tự động kiểm tra và cập nhật trạng thái thanh toán
     */
    public function autoVerifyPayment($order)
    {
        try {
            // Kiểm tra nếu đã thanh toán rồi thì không cần kiểm tra nữa
            if ($order->payment_status === 'paid') {
                return ['status' => 'already_paid'];
            }

            // Kiểm tra nếu đã quá hạn
            $paymentData = json_decode($order->payment_data, true);
            $expiresAt = isset($paymentData['expires_at']) ? Carbon::parse($paymentData['expires_at']) : null;

            if ($expiresAt && Carbon::now()->gt($expiresAt)) {
                $order->update([
                    'payment_status' => 'expired',
                    'order_status' => 'cancelled'
                ]);
                return ['status' => 'expired'];
            }

            // Kiểm tra giao dịch ngân hàng
            $bankCheck = $this->checkBankTransaction($order);

            if ($bankCheck['status'] === 'paid') {
                // Cập nhật trạng thái đã thanh toán
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'confirmed',
                    'paid_at' => now(),
                    'payment_reference' => $bankCheck['transaction_id'] ?? null,
                    'payment_data' => json_encode(array_merge(
                        json_decode($order->payment_data, true) ?? [],
                        ['bank_transaction' => $bankCheck]
                    ))
                ]);

                Log::info('Payment verified for order: ' . $order->order_number, $bankCheck);

                return ['status' => 'paid', 'transaction' => $bankCheck];
            }

            return ['status' => 'pending'];

        } catch (\Exception $e) {
            Log::error('Auto verify payment error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Xác minh thanh toán thủ công cho admin
     */
    public function manualVerifyPayment($orderId, $transactionId = null, $adminNote = null)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return ['success' => false, 'message' => 'Order not found'];
            }

            if ($order->payment_status === 'paid') {
                return ['success' => false, 'message' => 'Order already paid'];
            }

            // Cập nhật trạng thái thành công
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'paid_at' => now(),
                'payment_reference' => $transactionId,
                'payment_data' => json_encode(array_merge(
                    json_decode($order->payment_data, true) ?? [],
                    [
                        'manual_verification' => [
                            'verified_at' => now(),
                            'transaction_id' => $transactionId,
                            'admin_note' => $adminNote,
                            'verified_by' => auth()->id() ?? 'system'
                        ]
                    ]
                ))
            ]);

            Log::info('Manual payment verification completed', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'verified_by' => auth()->id() ?? 'system',
                'admin_note' => $adminNote
            ]);

            return [
                'success' => true,
                'message' => 'Payment verified manually',
                'order_id' => $orderId,
                'payment_status' => 'paid'
            ];

        } catch (\Exception $e) {
            Log::error('Manual payment verification error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Manual verification failed: ' . $e->getMessage()
            ];
        }
    }
}
