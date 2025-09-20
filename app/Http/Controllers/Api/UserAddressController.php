<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'is_default' => 'boolean',
            'type' => ['required', Rule::in(['billing', 'shipping', 'both'])]
        ]);

        $validated['user_id'] = Auth::id();

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = UserAddress::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserAddress $userAddress): JsonResponse
    {
        // Check if address belongs to authenticated user
        if ($userAddress->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $userAddress
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserAddress $userAddress): JsonResponse
    {
        // Check if address belongs to authenticated user
        if ($userAddress->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'postcode' => 'sometimes|required|string|max:20',
            'mobile' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
            'notes' => 'nullable|string',
            'is_default' => 'boolean',
            'type' => ['sometimes', 'required', Rule::in(['billing', 'shipping', 'both'])]
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Auth::user()->addresses()->where('id', '!=', $userAddress->id)->update(['is_default' => false]);
        }

        $userAddress->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $userAddress->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAddress $userAddress): JsonResponse
    {
        // Check if address belongs to authenticated user
        if ($userAddress->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $userAddress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(UserAddress $userAddress): JsonResponse
    {
        // Check if address belongs to authenticated user
        if ($userAddress->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Unset other defaults
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this as default
        $userAddress->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated successfully',
            'data' => $userAddress->fresh()
        ]);
    }

    /**
     * Get default billing address
     */
    public function getDefaultBilling(): JsonResponse
    {
        $address = Auth::user()->defaultBillingAddress();

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    /**
     * Get default shipping address
     */
    public function getDefaultShipping(): JsonResponse
    {
        $address = Auth::user()->defaultShippingAddress();

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }
}
