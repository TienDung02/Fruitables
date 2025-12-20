<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\UserNotification;
use App\Models\Ward;
use App\Models\User;
//use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the profile page
     */
//    public function show(string $username): Response
    public function index(): Response
    {
        $user = auth()->user();
//        $user = User::where('username', $username)->firstOrFail();
        Log::info('User Profile Accessed', $user->toArray());

        return Inertia::render('Frontend/Profile/Index', [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'dob' => $user->dob?->format('Y-m-d'),
                    'avatar' => $user->avatar,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                ]
            ],
            'notifications' => $this->getUserNotifications(),
            'orders' => $this->getUserOrders(),
            'addresses' => $this->getUserAddresses(),
        ]);
    }

    /**
     * Get user notifications
     */
    private function getUserNotifications()
    {
        return UserNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification->type),
                ];
            });
    }

    /**
     * Get user orders
     */
    private function getUserOrders()
    {
        return Order::with(['orderItems.productVariant.product.media'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at->format('d/m/Y'),
                    'items' => $order->orderItems->map(function ($item) {
                        $product = $item->productVariant->product;
                        return [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'variant_size' => $item->productVariant->size,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'image' => $product->media->first()?->file_path,
                        ];
                    }),
                    'status_badge' => $this->getOrderStatusBadge($order->status),
                ];
            });
    }

    /**
     * Get user addresses
     */
    private function getUserAddresses()
    {
        return UserAddress::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($address) {
                $ward_id = $address->ward_id;
                $ward = Ward::where('id', $ward_id)->first();
                $ward_name = $ward->name;
                $district_name = $ward->district->name;
                $province_name = $ward->province->name;
                Log::info('ward', $ward->toArray());
                return [
                    'id' => $address->id,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'ward' => $ward_name,
                    'district' => $district_name,
                    'province' => $province_name,
                    'is_default' => $address->is_default,
                ];
            });
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();
        $user->update($request->only(['full_name', 'phone', 'gender', 'dob']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Add new address
     */
    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'ward_id' => 'required|exists:wards,id',
            'district_id' => 'required|exists:districts,id',
            'province_id' => 'required|exists:provinces,id',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            UserAddress::where('user_id', auth()->id())
                ->update(['is_default' => false]);
        }

        UserAddress::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'ward_id' => $request->ward_id,
            'district_id' => $request->district_id,
            'province_id' => $request->province_id,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
        ]);
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, $id)
    {
        $address = UserAddress::where('user_id', auth()->id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'ward_id' => 'required|exists:wards,id',
            'district_id' => 'required|exists:districts,id',
            'province_id' => 'required|exists:provinces,id',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            UserAddress::where('user_id', auth()->id())
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($request->only([
            'name', 'phone', 'address', 'ward_id', 'district_id', 'province_id', 'is_default'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
        ]);
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        $address = UserAddress::where('user_id', auth()->id())->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        UserNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */


    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        return match($type) {
            'order' => 'bi-bag',
            'delivery' => 'bi-truck',
            'promotion' => 'bi-tag-fill',
            'system' => 'bi-bell-fill',
            default => 'bi-bell',
        };
    }

    /**
     * Get order status badge class
     */
    private function getOrderStatusBadge($status)
    {
        return match($status) {
            'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Pending'],
            'confirmed' => ['class' => 'bg-info', 'text' => 'Confirmed'],
            'processing' => ['class' => 'bg-primary', 'text' => 'Processing'],
            'shipping' => ['class' => 'bg-warning text-dark', 'text' => 'Shipping'],
            'delivered' => ['class' => 'bg-success', 'text' => 'Delivered'],
            'cancelled' => ['class' => 'bg-danger', 'text' => 'Cancelled'],
            default => ['class' => 'bg-secondary', 'text' => ucfirst($status)],
        };
    }
}
