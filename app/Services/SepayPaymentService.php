<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class SepayPaymentService
{
    private $secretKey;
    private $accountNumber;


    public function __construct()
    {
        $this->secretKey = config('services.sepay.secret_key');
        $this->accountNumber = config('services.sepay.account_number');
    }

    /**
     * Xác minh webhook từ SePay
     */
//    public function verifyWebhook($data)
//    {
//        try {
//            // SePay webhook verification
//            if (!isset($data['signature']) || !isset($data['content'])) {
//                return false;
//            }
//
//            $expectedSignature = hash_hmac('sha256', $data['content'] . $data['amount'], $this->secretKey);
//
//            return hash_equals($expectedSignature, $data['signature']);
//
//        } catch (\Exception $e) {
//            Log::error('SePay Webhook Verification Error', [
//                'error' => $e->getMessage(),
//                'data' => $data
//            ]);
//            return false;
//        }
//    }

    /**
     * Tạo nội dung chuyển khoản
     */
//    private function generateTransferContent($orderId, $orderInfo)
//    {
//        // Format: ORDER_ID_TIMESTAMP
//        return 'PAY_' . $orderId . '_' . time();
//    }

    /**
     * Tạo QR Code banking theo chuẩn VietQR
     */

//    private function generateVietQRContent($bankCode, $accountNumber, $amount, $content)
//    {
//        // Tạo nội dung VietQR chuẩn theo quy định
//        $amount = intval($amount);
//        $content = substr($content, 0, 25); // Giới hạn độ dài nội dung
//        $accountName = config('services.sepay.account_name', 'SEPAY VIETNAM');
//
//        // Đảm bảo amount là số nguyên và không có dấu phẩy
//        $formattedAmount = (string) intval($amount);
//
//        // VietQR format chuẩn
//        $vietQR = [
//            '00' => '01', // Payload Format Indicator
//            '01' => '12', // Point of Initiation Method (12 = QR hiển thị nhiều lần)
//            '38' => [ // Merchant Account Information (VietQR)
//                '00' => 'A000000727', // VietQR GUID
//                '01' => $bankCode,     // Bank Code (VD: 970422 cho MB Bank)
//                '02' => $accountNumber // Account Number
//            ],
//            '53' => '704',           // Transaction Currency (704 = VND)
//            '54' => $formattedAmount, // Transaction Amount (số nguyên, không có dấu phẩy)
//            '58' => 'VN',            // Country Code
//            '62' => [                // Additional Data
//                '08' => substr($content, 0, 25) // Bill Number/Content (giới hạn 25 ký tự)
//            ]
//        ];
//
//        // Tạo VietQR string
//        return $this->buildVietQRString($vietQR);
//    }

    /**
     * Build VietQR string từ array
     */
//    private function buildVietQRString($data)
//    {
//        $result = '';
//
//        foreach ($data as $tag => $value) {
//            if (is_array($value)) {
//                // Sub-field (như tag 38, 62)
//                $subData = '';
//                foreach ($value as $subTag => $subValue) {
//                    $subData .= $subTag . str_pad(strlen($subValue), 2, '0', STR_PAD_LEFT) . $subValue;
//                }
//                $result .= $tag . str_pad(strlen($subData), 2, '0', STR_PAD_LEFT) . $subData;
//            } else {
//                $result .= $tag . str_pad(strlen($value), 2, '0', STR_PAD_LEFT) . $value;
//            }
//        }
//
//        // Thêm CRC (tag 63)
//        $crc = $this->calculateCRC16($result . '6304');
//        $result .= '63' . '04' . $crc;
//
//        return $result;
//    }

    /**
     * Tính CRC16 cho VietQR
     */
//    private function calculateCRC16($data)
//    {
//        $polynomial = 0x1021;
//        $crc = 0xFFFF;
//
//        for ($i = 0; $i < strlen($data); $i++) {
//            $crc ^= (ord($data[$i]) << 8);
//            for ($j = 0; $j < 8; $j++) {
//                if ($crc & 0x8000) {
//                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
//                } else {
//                    $crc = ($crc << 1) & 0xFFFF;
//                }
//            }
//        }
//
//        return strtoupper(sprintf('%04X', $crc));
//    }

    /**
     * Kiểm tra trạng thái giao dịch thông qua SePay API thực tế
     */
    public function checkTransactionStatus($orderId)
    {
        try {
            $apiToken = config('services.sepay.api_token');  // ← Đổi sang api_token
            $accountNumber = config('services.sepay.account_number');

//            Log::info('SePay Config Check', [
//                'has_api_token' => !empty($apiToken),
//                'api_token_length' => strlen($apiToken ?? ''),
//                'api_token_first_20' => substr($apiToken ?? '', 0, 20) . '...',
//                'account_number' => $accountNumber
//            ]);

            if (!$apiToken || !$accountNumber) {
                return ['status' => 'error', 'message' => 'SePay not configured'];
            }

            // ✅ Format header ĐÚNG theo tài liệu SePay
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,  // ← ĐÚNG
                'Content-Type' => 'application/json'
            ])->timeout(10)->get('https://my.sepay.vn/userapi/transactions/list', [
                'account_number' => $accountNumber,
                'limit' => 50
            ]);

            if (!$response->successful()) {
//                Log::error('SePay API failed', [
//                    'status' => $response->status(),
//                    'body' => $response->body()
//                ]);
                return ['status' => 'error', 'message' => 'API call failed'];
            }

            $transactions = $response->json()['transactions'] ?? [];
            $order = \App\Models\Order::find($orderId);

            if (!$order) {
                return ['status' => 'error', 'message' => 'Order not found'];
            }

            $matched = $this->findMatchingTransaction($transactions, $order);

            if ($matched) {
                return [
                    'status' => 'paid',
                    'transaction_id' => $matched['id'],
                    'amount' => $matched['amount_in'],
                    'transaction_date' => $matched['transaction_date']
                ];
            }

            return ['status' => 'pending'];

        } catch (\Exception $e) {
            Log::error('checkTransactionStatus error', [
                'error' => $e->getMessage()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    private function findMatchingTransaction($transactions, $order)
    {
        $orderAmount = (float) $order->total;
        $orderId = strtoupper($order->id);
        $orderIdNormalized = str_replace('_', '', $orderId); // ✅ Đặt ở đầu
        $orderCreatedAt = Carbon::parse($order->created_at);

        Log::info('Searching for matching transaction', [
            'order_id' => $orderId,
            'order_id_normalized' => $orderIdNormalized,
            'order_amount' => $orderAmount,
            'total_transactions' => count($transactions)
        ]);

        foreach ($transactions as $transaction) {
            $transactionAmount = (float) ($transaction['amount_in'] ?? 0);
            $transactionContent = strtoupper($transaction['transaction_content'] ?? '');
            $transactionDate = Carbon::parse($transaction['transaction_date']);

            Log::debug('Checking transaction', [
                'transaction_id' => $transaction['id'],
                'amount' => $transactionAmount,
                'content' => $transactionContent,
                'date' => $transactionDate
            ]);

            // 1. Kiểm tra amount hợp lệ
            if ($orderAmount <= 0) {
                Log::error('Order amount is zero or negative', [
                    'order_id' => $orderId,
                    'order_amount' => $orderAmount
                ]);
                break;
            }

            // 2. Kiểm tra order ID trong content (cả 2 format: có _ và không có _)
            if (strpos($transactionContent, $orderId) === false &&
                strpos($transactionContent, $orderIdNormalized) === false) {
                Log::debug('Order ID not found in content', [
                    'expected_original' => $orderId,
                    'expected_normalized' => $orderIdNormalized,
                    'content' => $transactionContent
                ]);
                continue;
            }else{
                Log::info('Order ID found in transaction content', [
                    'order_id' => $orderId,
                    'transaction_content' => $transactionContent
                ]);
            }


            // 3. Kiểm tra số tiền khớp
            if (abs($transactionAmount - $orderAmount) > 1) {
                Log::debug('Amount mismatch', [
                    'expected' => $orderAmount,
                    'actual' => $transactionAmount
                ]);
                continue;
            }else{
                Log::info('Amount matches', [
                    'order_id' => $orderId,
                    'amount' => $transactionAmount
                ]);
            }

            // 4. Kiểm tra thời gian giao dịch phải sau khi tạo đơn
            if ($transactionDate->lt($orderCreatedAt)) {
                Log::debug('Transaction date before order creation', [
                    'transaction_date' => $transactionDate,
                    'order_created_at' => $orderCreatedAt
                ]);
                continue;
            }else{
                Log::info('Transaction date is valid', [
                    'transaction_date' => $transactionDate,
                    'order_created_at' => $orderCreatedAt
                ]);
            }

            // ✅ Tất cả điều kiện đều khớp!
            Log::info('🎉 Found matching transaction!', [
                'transaction_id' => $transaction['id'],
                'order_id' => $orderId,
                'amount' => $transactionAmount
            ]);

            return $transaction;
        }

        Log::info('No matching transaction found', [
            'order_id' => $orderId
        ]);

        return null;
    }

    /**
     * Tính điểm khớp giữa nội dung CK và mã đơn hàng
     * Return: 0.0 - 1.0
     */
    private function calculateMatchScore($txContent, $orderId)
    {
        // Remove spaces và special characters
        $cleanTx = preg_replace('/[^A-Z0-9]/', '', $txContent);
        $cleanOrder = preg_replace('/[^A-Z0-9]/', '', $orderId);

        // 1. Exact match
        if (strpos($cleanTx, $cleanOrder) !== false) {
            return 1.0;
        }

        // 2. Extract parts của order number: ORD_1766454342_mP0VNLae
        preg_match('/(\d{10,})/', $orderId, $matches); // Lấy số đơn hàng
        $orderNumericPart = $matches[1] ?? '';

        // 3. Nếu có số đơn hàng trong nội dung CK
        if ($orderNumericPart && strpos($cleanTx, $orderNumericPart) !== false) {
            return 0.8; // 80% khớp
        }

        // 4. Similarity check (Levenshtein distance)
        $similarity = 0;
        similar_text($cleanTx, $cleanOrder, $similarity);

        return $similarity / 100;
    }

    /**
     * Tạo QR code nhanh cho số tiền bất kỳ
     */
}
