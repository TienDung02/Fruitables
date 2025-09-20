<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoPaymentService
{
    private $partnerCode = 'MOMOBKUN20180529';
    private $accessKey = 'klm05TvNBzhg7h7j';
    private $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    private $endpoint = 'https://test-payment.momo.vn';
    private $returnUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->returnUrl = config('app.url') . '/payment/momo/return';
        $this->notifyUrl = config('app.url') . '/api/payment/momo/notify';
    }

    /**
     * Tạo thanh toán MoMo và trả về QR code
     */
    public function createPayment($orderId, $amount, $orderInfo, $extraData = "")
    {
        $amount = 1000;
        $requestId = time() . "";
        $requestType = "captureWallet";

        // Tạo chữ ký
        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $this->notifyUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $this->partnerCode .
                   "&redirectUrl=" . $this->returnUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Fruitables Store",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl' => $this->notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            Log::info('Sending MoMo Payment Request', [
                'endpoint' => $this->endpoint . '/v2/gateway/api/create',
                'data' => $data
            ]);

            $response = Http::post($this->endpoint . '/v2/gateway/api/create', $data);

            Log::info('MoMo Payment Response Status', ['status' => $response->status()]);
            Log::info('MoMo Payment Response Body', $response->json());

            if ($response->successful()) {
                $result = $response->json();

                Log::info('MoMo Result Code', ['resultCode' => $result['resultCode'] ?? 'not_found']);
                Log::info('MoMo Full Response', $result);

                if ($result['resultCode'] == 0) {
                    // Log từng field để debug
                    Log::info('MoMo Success Response Fields', [
                        'payUrl' => $result['payUrl'] ?? 'not_found',
                        'qrCodeUrl' => $result['qrCodeUrl'] ?? 'not_found',
                        'deeplink' => $result['deeplink'] ?? 'not_found',
                        'deeplinkMiniApp' => $result['deeplinkMiniApp'] ?? 'not_found'
                    ]);

                    // Tạo QR code URL từ payUrl nếu không có qrCodeUrl
                    $qrCodeUrl = $result['qrCodeUrl'] ?? null;
                    if (!$qrCodeUrl && isset($result['payUrl'])) {
                        // Tạo QR code URL từ payUrl bằng QR code generator
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($result['payUrl']);
                        Log::info('Generated QR Code URL from payUrl', ['qrCodeUrl' => $qrCodeUrl]);
                    }

                    return [
                        'success' => true,
                        'payUrl' => $result['payUrl'],
                        'qrCodeUrl' => $qrCodeUrl,
                        'deeplink' => $result['deeplink'] ?? null,
                        'deeplinkMiniApp' => $result['deeplinkMiniApp'] ?? null,
                        'requestId' => $requestId,
                        'orderId' => $orderId,
                        'fullResponse' => $result // Thêm full response để debug
                    ];
                } else {
                    Log::error('MoMo API Error', [
                        'resultCode' => $result['resultCode'],
                        'message' => $result['message'] ?? 'Unknown error'
                    ]);

                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Lỗi không xác định từ MoMo',
                        'resultCode' => $result['resultCode']
                    ];
                }
            } else {
                Log::error('MoMo HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Không thể kết nối đến MoMo',
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('MoMo Payment Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xác minh chữ ký từ MoMo callback
     */
    public function verifySignature($data)
    {
        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . $data['amount'] .
                   "&extraData=" . $data['extraData'] .
                   "&message=" . $data['message'] .
                   "&orderId=" . $data['orderId'] .
                   "&orderInfo=" . $data['orderInfo'] .
                   "&orderType=" . $data['orderType'] .
                   "&partnerCode=" . $data['partnerCode'] .
                   "&payType=" . $data['payType'] .
                   "&requestId=" . $data['requestId'] .
                   "&responseTime=" . $data['responseTime'] .
                   "&resultCode=" . $data['resultCode'] .
                   "&transId=" . $data['transId'];

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return $signature === $data['signature'];
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus($orderId, $requestId)
    {
        $rawHash = "accessKey=" . $this->accessKey .
                   "&orderId=" . $orderId .
                   "&partnerCode=" . $this->partnerCode .
                   "&requestId=" . $requestId;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'signature' => $signature,
            'lang' => 'vi'
        ];

        try {
            $response = Http::post($this->endpoint . '/v2/gateway/api/query', $data);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('MoMo Check Status Error', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
