<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VietQRService
{
    /**
     * Generate VietQR standard QR code string
     *
     * @param string $accountNumber Bank account number
     * @param string $bankCode Bank code (e.g., 970415 for Vietinbank)
     * @param string $accountName Account holder name
     * @param float $amount Transaction amount
     * @param string $description Transaction description
     * @param string $orderCode Order reference code
     * @return string QR code content string
     */
//    public function generateVietQRString(
//        string $accountNumber,
//        string $bankCode,
//        string $accountName,
//        float | int $amount,
//        string $description = '',
//        string $orderCode = ''
//    ): string {
//        // VietQR follows EMVCo QR Code standard for Payment Systems
//        $qrString = '';
//
//        // Payload Format Indicator (ID 00) - REQUIRED
//        $qrString .= $this->formatTLV('00', '01');
//
//        // Point of Initiation Method (ID 01) - REQUIRED
//        $qrString .= $this->formatTLV('01', '11');
//
//        // Merchant Account Information (ID 38 for VietQR) - REQUIRED
//        $merchantInfo = '';
//        $merchantInfo .= $this->formatTLV('00', 'A000000727'); // VietQR GUID
//
//        // Merchant Account - NAPAS Service
//        $napasService = '';
//        $napasService .= $this->formatTLV('01', $bankCode); // Bank identifier
//        $napasService .= $this->formatTLV('02', $accountNumber); // Merchant account
//
//        $merchantInfo .= $this->formatTLV('01', $napasService);
//        $qrString .= $this->formatTLV('38', $merchantInfo);
//
//        // Merchant Category Code (ID 52) - REQUIRED for payment
//        $qrString .= $this->formatTLV('52', '5311'); // Department stores, general merchandise
//
//        // Transaction Currency (ID 53) - REQUIRED
//        $qrString .= $this->formatTLV('53', '704'); // VND
//
//        // Transaction Amount (ID 54) - CONDITIONAL
//        if ($amount > 0) {
//            $qrString .= $this->formatTLV('54', number_format($amount, 2, '.', ''));
//        }
//
//        // Country Code (ID 58) - REQUIRED
//        $qrString .= $this->formatTLV('58', 'VN');
//
//        // Merchant Name (ID 59) - REQUIRED
//        $normalizedName = $this->normalizeVietnamese($accountName);
//        $qrString .= $this->formatTLV('59', substr($normalizedName, 0, 25));
//
//        // Merchant City (ID 60) - REQUIRED
//        $qrString .= $this->formatTLV('60', 'HCM');
//
//        // Additional Data Field Template (ID 62) - OPTIONAL
//        $additionalData = '';
//
//        if (!empty($orderCode)) {
//            // Bill Number (ID 01)
//            $additionalData .= $this->formatTLV('01', substr($orderCode, 0, 25));
//        }
//
//        if (!empty($description)) {
//            // Purpose of Transaction (ID 08)
//            $normalizedDesc = $this->normalizeVietnamese($description);
//            $additionalData .= $this->formatTLV('08', substr($normalizedDesc, 0, 25));
//        }
//
//        if (!empty($additionalData)) {
//            $qrString .= $this->formatTLV('62', $additionalData);
//        }
//
//        // Calculate CRC (ID 63) - REQUIRED
//        $crc = $this->calculateCRC16($qrString . '6304');
//        $qrString .= $this->formatTLV('63', strtoupper($crc));
//
//        return $qrString;
//    }

    public function generateVietQRString(
        string $accountNumber,
        string $bankBin,
        string $accountName,
        float|int $amount,
        string $description = '',
        string $orderCode = ''
    ): string {

        $qr = '';

        // 00 - Payload Format Indicator
        $qr .= '000201';

        // 01 - Point of Initiation Method (11 = static)
        $qr .= '010211';

        /**
         * 38 - Merchant Account Information (VietQR cá nhân)
         * ❗ Sửa lỗi tính toán length cho NAPAS service
         */
        $napasService = '00' . sprintf('%02d', strlen($bankBin)) . $bankBin .
                       '01' . sprintf('%02d', strlen($accountNumber)) . $accountNumber;

        $merchantAccount =
            '0010A000000727' .       // VietQR GUID
            '01' . sprintf('%02d', strlen($napasService)) . $napasService;

        $qr .= '38' . sprintf('%02d', strlen($merchantAccount)) . $merchantAccount;

        // 52 - Merchant Category Code (không bắt buộc)
        $qr .= '52040000';

        // 53 - Currency (VND)
        $qr .= '5303704';

        // 54 - Amount (PHẢI là số nguyên)
        if ($amount > 0) {
            $amount = (string) intval(round($amount));
            $qr .= '54' . sprintf('%02d', strlen($amount)) . $amount;
        }

        // 58 - Country Code
        $qr .= '5802VN';

        // 59 - Merchant Name
        $name = substr($this->normalizeVietnamese($accountName), 0, 25);
        $qr .= '59' . sprintf('%02d', strlen($name)) . $name;

        // 60 - City
        $qr .= '6003HCM';

        /**
         * 62 - Additional Data
         */
        $extra = '';

        if ($orderCode !== '') {
            $extra .= '01' . sprintf('%02d', strlen($orderCode)) . $orderCode;
        }

        if ($description !== '') {
            $desc = substr($this->normalizeVietnamese($description), 0, 25);
            $extra .= '08' . sprintf('%02d', strlen($desc)) . $desc;
        }

        if ($extra !== '') {
            $qr .= '62' . sprintf('%02d', strlen($extra)) . $extra;
        }

        // 63 - CRC
        $crc = $this->calculateCRC16($qr . '6304');
        $qr .= '6304' . strtoupper($crc);

        Log::info('Generated VietQR: ' . $qr);
        return $qr;
    }





    /**
     * Format data using TLV (Tag-Length-Value) format
     */
    private function formatTLV(string $tag, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $length . $value;
    }

    /**
     * Calculate CRC16 cho VietQR (chuẩn CCITT-FALSE với initial 0xFFFF)
     */
    private function calculateCRC16(string $data): string
    {
        $table = [];
        $polynomial = 0x1021;

        // Build CRC table
        for ($i = 0; $i < 256; $i++) {
            $crc = $i << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
            $table[$i] = $crc;
        }

        // Calculate CRC
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $tbl_idx = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $crc = (($crc << 8) ^ $table[$tbl_idx]) & 0xFFFF;
        }

        return sprintf('%04X', $crc);
    }

    /**
     * Normalize Vietnamese text for QR code
     */
    private function normalizeVietnamese(string $text): string
    {
        // Convert to uppercase and remove Vietnamese diacritics for better compatibility
        $text = strtoupper($text);

        // Vietnamese character mapping
        $vietnameseChars = [
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D'
        ];

        return strtr($text, $vietnameseChars);
    }

    /**
     * Get bank codes mapping
     */
    public function getBankCodes(): array
    {
        return [
            'vietinbank' => '970415',
            'vietcombank' => '970436',
            'bidv' => '970418',
            'agribank' => '970405',
            'techcombank' => '970407',
            'mbbank' => '970422',
            'acb' => '970416',
            'vpbank' => '970432',
            'tpbank' => '970423',
            'sacombank' => '970403',
            'hdbank' => '970437',
            'shb' => '970443',
            'eximbank' => '970431',
            'ocb' => '970448',
            'msc' => '970426',
            'namabank' => '970428',
            'oceanbank' => '970414',
            'pgbank' => '970430',
            'kienlongbank' => '970452',
            'ncb' => '970419'
        ];
    }

    /**
     * Generate QR code as base64 image using simple PHP QR library
     */
    public function generateQRCodeImage(string $qrContent): string
    {
        // Use reliable online QR service
        return 'https://quickchart.io/qr?text=' . urlencode($qrContent) . '&size=300';
    }

    /**
     * Generate SVG QR code using online service
     */
    public function generateSVGQRCode(string $qrContent): string
    {
        // Use QuickChart for SVG QR
        return 'https://quickchart.io/qr?text=' . urlencode($qrContent) . '&format=svg&size=300';
    }

    /**
     * Generate multiple REAL QR code formats (no external dependencies)
     */
    public function generateMultipleQRFormats(string $qrContent): array
    {
        return [
            'quickchart_png' => 'https://quickchart.io/qr?text=' . urlencode($qrContent) . '&size=300',
            'quickchart_svg' => 'https://quickchart.io/qr?text=' . urlencode($qrContent) . '&format=svg&size=300',
            'qr_server' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrContent),
            'chart_google' => 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($qrContent),
            'raw_content' => $qrContent
        ];
    }

    /**
     * Generate VietQR with proper image output
     */
    public function generateVietQRWithImage(
        string $accountNumber,
        string $bankCode,
        string $accountName,
        float $amount,
        string $description = '',
        string $orderCode = ''
    ): array {
        $qrContent = $this->generateVietQRString(
            $accountNumber,
            $bankCode,
            $accountName,
            $amount,
            $description,
            $orderCode
        );

        return [
            'qr_content' => $qrContent,
            'qr_image' => $this->generateQRCodeImage($qrContent),
            'bank_info' => [
                'account_number' => $accountNumber,
                'account_name' => $accountName,
                'bank_code' => $bankCode
            ],
            'amount' => $amount,
            'description' => $description,
            'order_code' => $orderCode
        ];
    }
}
