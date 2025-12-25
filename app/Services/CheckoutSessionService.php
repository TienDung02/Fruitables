<?php

namespace App\Services;

use App\Models\UserAddress;
use App\Models\Ward;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CheckoutSessionService
{
    const SESSION_KEY = 'checkout_shipping_info';

    /**
     * Lưu thông tin nhận hàng vào session
     */
    public function saveShippingInfo(array $shippingInfo): void
    {
        Session::put(self::SESSION_KEY, $shippingInfo);
    }

    /**
     * Lấy thông tin nhận hàng từ session
     */
    public function getShippingInfo(): ?array
    {
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Xóa thông tin nhận hàng khỏi session
     */
    public function clearShippingInfo(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Sync thông tin từ database nếu user đã đăng nhập
     * Trả về thông tin bao gồm tất cả địa chỉ của user
     */
    public function syncFromDatabase(): ?array
    {
//        Log::info('syncFromDatabase is called in CheckoutSessionService (after login)');
        $user = Auth::user();

        if (!$user) {
            return $this->getShippingInfo();
        }
//        Log::info('Getting shipping info for user', ['user_id' => $user->id]);

        // Lấy tất cả địa chỉ của user
        $allAddresses = UserAddress::where('user_id', $user->id)
            ->with(['ward.district.province'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();


        // Format tất cả địa chỉ
        $formattedAddresses = $allAddresses->map(function ($address) {
            $ward = Ward::query()->with('district.province')->find($address->ward_id);
            return [
                'id' => $address->id,
                'name' => $address->name,
                'phone' => $address->phone,
                'address' => $address->address,
                'ward_id' => $address->ward_id,
                'synced_from_db' => true,
                'user_id' => $address->user_id,
                'full_address' => $address->address . ', ' .
                                ($ward->name ?? '') . ', ' .
                                ($ward->district->name ?? '') . ', ' .
                                ($ward->district->province->name ?? '')
            ];
        })->toArray();

        // Lấy địa chỉ mặc định (để làm current shipping info)
        $defaultAddress = $allAddresses->where('is_default', true)->first();

//        Log::info('$defaultAddress', ['default_address' => $defaultAddress]);

        if (!$defaultAddress) {
            // Nếu không có địa chỉ mặc định, lấy địa chỉ đầu tiên
            $defaultAddress = $allAddresses->first();
        }

        $sessionShippingInfo = $this->getShippingInfo();
        if ($sessionShippingInfo === null) {
            return $defaultAddress ? [
                'id' => $defaultAddress->id,
                'name' => $defaultAddress->name,
                'phone' => $defaultAddress->phone,
                'address' => $defaultAddress->address,
                'ward_id' => $defaultAddress->ward_id,
                'synced_from_db' => true,
                'user_id' => $defaultAddress->user_id,
                'full_address' => $defaultAddress->address . ', ' .
                    ($defaultAddress->ward->name ?? '') . ', ' .
                    ($defaultAddress->ward->district->name ?? '') . ', ' .
                    ($defaultAddress->ward->district->province->name ?? '')
            ] : null;
        }
        Log::info('Session shipping info before sync', ['shipping_info' => $sessionShippingInfo]);

        // Tạo thông tin shipping đầy đủ bao gồm tất cả địa chỉ
        if ($defaultAddress) {
            $ward = Ward::query()->with('district.province')->find($defaultAddress->ward_id);
            $shippingInfo = [
                // Thông tin shipping hiện tại (từ địa chỉ mặc định)
                'current' => [
                    'name' => $defaultAddress->name,
                    'phone' => $defaultAddress->phone,
                    'address' => $defaultAddress->address,
                    'ward_id' => $defaultAddress->ward_id,
                    'full_address' => $defaultAddress->address . ', ' .
                        ($ward->name ?? '') . ', ' .
                        ($ward->district->name ?? '') . ', ' .
                        ($ward->district->province->name ?? ''),
                    'selected_address_id' => $defaultAddress->id
                ],
                // Tất cả địa chỉ của user
                'all_addresses' => $formattedAddresses,
                'synced_from_db' => true,
                'user_id' => $user->id,
                'total_addresses' => count($formattedAddresses)
            ];
        } else {
            // Nếu không có địa chỉ trong DB
            $shippingInfo = [
                'current' => [
                    'name' => $user->name ?? '',
                    'phone' => $user->phone ?? '',
                    'address' => '',
                    'full_address' => $defaultAddress->address . ', ' .
                        ($ward->name ?? '') . ', ' .
                        ($ward->district->name ?? '') . ', ' .
                        ($ward->district->province->name ?? ''),
                    'ward_id' => null,
                    'selected_address_id' => null
                ],
                'all_addresses' => [],
                'synced_from_db' => true,
                'user_id' => $user->id,
                'total_addresses' => 0
            ];
        }

        // Cập nhật danh sách địa chỉ mới nhất
        $sessionShippingInfo['all_addresses'] = $formattedAddresses;
        $sessionShippingInfo['total_addresses'] = count($formattedAddresses);
        $this->saveShippingInfo($shippingInfo);
        $sessionShippingInfo = $this->getShippingInfo();
        $this->saveShippingInfo($sessionShippingInfo);
        $shippingInfo = $sessionShippingInfo;
        return $shippingInfo;
    }

    /**
     * Kiểm tra xem thông tin có hợp lệ không
     */
    public function validateShippingInfo(array $shippingInfo): array
    {
        $errors = [];

        if (empty($shippingInfo['full_name'])) {
            $errors['full_name'] = 'Họ tên là bắt buộc';
        }

        if (empty($shippingInfo['phone'])) {
            $errors['phone'] = 'Số điện thoại là bắt buộc';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $shippingInfo['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($shippingInfo['emails'])) {
            $errors['emails'] = 'Email là bắt buộc';
        } elseif (!filter_var($shippingInfo['emails'], FILTER_VALIDATE_EMAIL)) {
            $errors['emails'] = 'Email không hợp lệ';
        }

        if (empty($shippingInfo['address'])) {
            $errors['address'] = 'Địa chỉ là bắt buộc';
        }

        if (empty($shippingInfo['province_id'])) {
            $errors['province_id'] = 'Tỉnh/Thành phố là bắt buộc';
        }

        if (empty($shippingInfo['district_id'])) {
            $errors['district_id'] = 'Quận/Huyện là bắt buộc';
        }

        if (empty($shippingInfo['ward_id'])) {
            $errors['ward_id'] = 'Phường/Xã là bắt buộc';
        }

        return $errors;
    }

    /**
     * Chuẩn bị dữ liệu cho response
     */
    public function prepareResponseData(): array
    {
        $shippingInfo = $this->syncFromDatabase();

        return [
            'shipping_info' => $shippingInfo,
            'is_logged_in' => Auth::check(),
            'user_id' => Auth::id()
        ];
    }

    /**
     * Lấy tất cả địa chỉ của user đã đăng nhập
     */
    public function getAllUserAddresses(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        $allAddresses = UserAddress::where('user_id', $user->id)
            ->with(['ward.district.province'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $allAddresses->map(function ($address) {
            return [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'province_id' => $address->province_id,
                'district_id' => $address->district_id,
                'ward_id' => $address->ward_id,
                'province_name' => $address->province->name ?? '',
                'district_name' => $address->district->name ?? '',
                'ward_name' => $address->ward->name ?? '',
                'is_default' => $address->is_default,
                'notes' => $address->notes,
                'full_address' => $address->address_line . ', ' .
                                ($address->ward->name ?? '') . ', ' .
                                ($address->district->name ?? '') . ', ' .
                                ($address->province->name ?? '')
            ];
        })->toArray();
    }
}
