<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected array $addressData;

    public function __construct()
    {
        $jsonPath = public_path('data/indonesia-address.json');
        if (file_exists($jsonPath)) {
            $this->addressData = json_decode(file_get_contents($jsonPath), true);
        } else {
            $this->addressData = ['provinces' => [], 'cities' => [], 'districts' => [], 'villages' => []];
        }
    }

    public function provinces(): JsonResponse
    {
        return response()->json([
            'data' => $this->addressData['provinces'] ?? []
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $provinceCode = $request->query('province_code');
        
        if (!$provinceCode) {
            return response()->json([
                'data' => $this->addressData['cities'] ?? []
            ]);
        }

        $cities = array_filter($this->addressData['cities'] ?? [], function ($city) use ($provinceCode) {
            return $city['province_code'] === $provinceCode;
        });

        return response()->json([
            'data' => array_values($cities)
        ]);
    }

    public function districts(Request $request): JsonResponse
    {
        $cityCode = $request->query('city_code');
        
        if (!$cityCode) {
            return response()->json([
                'data' => []
            ]);
        }

        $districts = array_filter($this->addressData['districts'] ?? [], function ($district) use ($cityCode) {
            return $district['city_code'] === $cityCode;
        });
        
        return response()->json([
            'data' => array_values($districts)
        ]);
    }

    public function villages(Request $request): JsonResponse
    {
        $districtCode = $request->query('district_code');
        
        if (!$districtCode) {
            return response()->json([
                'data' => []
            ]);
        }

        $villages = array_filter($this->addressData['villages'] ?? [], function ($village) use ($districtCode) {
            return $village['district_code'] === $districtCode;
        });
        
        return response()->json([
            'data' => array_values($villages)
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = strtolower($request->query('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $results = [];

        foreach ($this->addressData['provinces'] ?? [] as $province) {
            if (str_contains(strtolower($province['name']), $query)) {
                $results[] = [
                    'type' => 'province',
                    'code' => $province['code'],
                    'name' => $province['name']
                ];
            }
        }

        foreach ($this->addressData['cities'] ?? [] as $city) {
            if (str_contains(strtolower($city['name']), $query)) {
                $province = $this->getProvinceByCode($city['province_code']);
                $results[] = [
                    'type' => 'city',
                    'code' => $city['code'],
                    'name' => $city['name'],
                    'province_name' => $province['name'] ?? null
                ];
            }
        }

        return response()->json([
            'data' => array_slice($results, 0, 20)
        ]);
    }

    public function geocode(Request $request, GeocodingService $geocodingService): JsonResponse
    {
        $address = $request->input('address');
        
        if (!$address) {
            return response()->json([
                'error' => 'Address is required'
            ], 400);
        }

        try {
            $result = $geocodingService->geocode($address);
            
            return response()->json([
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Geocoding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function getProvinceByCode(string $code): ?array
    {
        foreach ($this->addressData['provinces'] ?? [] as $province) {
            if ($province['code'] === $code) {
                return $province;
            }
        }
        return null;
    }
}
