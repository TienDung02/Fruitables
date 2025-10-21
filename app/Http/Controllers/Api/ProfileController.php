<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserNotification;
use App\Models\Order;

class ProfileController extends Controller
{
    /**
     * Get user profile information
     */
    public function show(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dob' => $user->dob,
                    'gender' => $user->gender,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile including avatar upload
     */
    public function update(Request $request)
    {
        Log::info('Profile update request received', $request->all());
        try {
            $user = Auth::user();

            // Validation rules
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable|string|max:255',
                'gender' => 'nullable|in:male,female,other',
                'dob' => 'nullable|date|before:today',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // 1MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update basic profile info
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }

            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }

            if ($request->has('dob')) {
                Log::info('DOB update request', ['dob' => $request->dob]);
                $user->dob = $request->dob;
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarFile = $request->file('avatar');

                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Generate unique filename
                $fileName = 'avatar/' . uniqid() . '_' . time() . '.' . $avatarFile->getClientOriginalExtension();

                // Store new avatar
                $avatarFile->storeAs('', $fileName, 'public');

                $user->avatar = $fileName;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'gender' => $user->gender,
                    'dob' => $user->dob,
                    'avatar' => $user->avatar ? 'storage/' . $user->avatar : null,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
                'new_password_confirmation' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password change error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to change password'
            ], 500);
        }
    }

    /**
     * Get user addresses
     */
    public function getAddresses()
    {
        try {
            $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get addresses'
            ], 500);
        }
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'ward_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'province_id' => 'nullable|integer',
                'is_default' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If this address is set as default, unset others
            if ($request->is_default) {
                UserAddress::where('user_id', Auth::id())
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'ward_id' => $request->ward_id,
                'district_id' => $request->district_id,
                'province_id' => $request->province_id,
                'is_default' => $request->is_default ?? false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully',
                'address' => $address
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add address'
            ], 500);
        }
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, $id)
    {
        try {
            $address = UserAddress::where('user_id', Auth::id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'ward_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'province_id' => 'nullable|integer',
                'is_default' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If this address is set as default, unset others
            if ($request->is_default) {
                UserAddress::where('user_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $address->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'ward_id' => $request->ward_id,
                'district_id' => $request->district_id,
                'province_id' => $request->province_id,
                'is_default' => $request->is_default ?? false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'address' => $address
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address'
            ], 500);
        }
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        try {
            $address = UserAddress::where('user_id', Auth::id())->findOrFail($id);
            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete address'
            ], 500);
        }
    }

    /**
     * Get user notifications
     */
    public function getNotifications()
    {
        try {
            $notifications = Auth::user()->notifications()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notifications'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        try {
            $notification = UserNotification::where('user_id', Auth::id())
                ->findOrFail($id);

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        try {
            UserNotification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }

    /**
     * Get user orders
     */
    public function getOrders()
    {
        try {
            $orders = Auth::user()->orders()
                ->with(['orderItems.productVariant.product', 'orderItems.productVariant.media'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    // Format order data for frontend
                    $firstItem = $order->orderItems->first();
                    Log::info('first item', $firstItem);
                    return [
                        'id' => $order->id,
                        'status' => $order->status,
                        'total_price' => number_format($order->total, 0, ',', '.'),
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'product_name' => $firstItem ? $firstItem->productVariant->product->name : 'N/A',
                        'variant' => $firstItem ? $firstItem->productVariant->name : 'N/A',
                        'quantity' => $firstItem ? $firstItem->quantity : 0,
                        'image' => $firstItem && $firstItem->productVariant->product->media->first()
                            ? $firstItem->productVariant->product->media->first()->file_path
                            : null
                    ];
                });
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get orders'
            ], 500);
        }
    }

    /**
     * Update user phone number
     */
    public function updatePhone(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|max:20|min:10|unique:users,phone,' . Auth::id(),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $oldPhone = $user->phone;

            // Update phone number
            $user->phone = $request->phone;
            $user->save();

            // Log the phone number change for security
            \Log::info('Phone number updated', [
                'user_id' => $user->id,
                'old_phone' => $oldPhone,
                'new_phone' => $request->phone,
                'updated_at' => now(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number updated successfully',
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'updated_at' => $user->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Phone update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update phone number',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
