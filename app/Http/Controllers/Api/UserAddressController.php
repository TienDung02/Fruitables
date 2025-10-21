<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    /**
     * Display a listing of user's addresses
     */
    public function index(): JsonResponse
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /**
     * Store a new address
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('request', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'ward_id' => 'required|integer|exists:wards,id',
            'label' => 'nullable|string|max:255',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        // If this is set as default, unset other default addresses
        if ($data['is_default'] ?? false) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = UserAddress::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * Display the specified address
     */
    public function show($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, $id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'ward_id' => 'required|integer|exists:wards,id',
            'label' => 'nullable|string|max:255',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // If this is set as default, unset other default addresses
        if ($data['is_default'] ?? false) {
            Auth::user()->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address->fresh()
        ]);
    }

    /**
     * Remove the specified address
     */
    public function destroy($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set address as default
     */
    public function setDefault($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        // Unset all other default addresses
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated successfully',
            'data' => $address->fresh()
        ]);
    }

    /**
     * Get location data for address form
     */
    public function getLocationData(): JsonResponse
    {
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => [
                'provinces' => $provinces
            ]
        ]);
    }

    /**
     * Get districts by province
     */
    public function getDistricts($provinceId): JsonResponse
    {
        $districts = District::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    /**
     * Get wards by district
     */
    public function getWards($districtId): JsonResponse
    {
        $wards = Ward::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $wards
        ]);
    }
}
