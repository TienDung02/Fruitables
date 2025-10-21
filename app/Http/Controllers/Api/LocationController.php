<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all provinces
     */
    public function getProvinces()
    {
        try {
            $provinces = Province::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provinces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get districts by province ID
     */
    public function getDistricts($provinceId)
    {
        try {
            $districts = District::where('province_id', $provinceId)
                ->select('id', 'name', 'province_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch districts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wards by district ID
     */
    public function getWards($districtId)
    {
        try {
            $wards = Ward::where('district_id', $districtId)
                ->select('id', 'name', 'district_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $wards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wards',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
