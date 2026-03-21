<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    protected const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    protected const CACHE_TTL = 86400 * 30; // 30 days
    protected const USER_AGENT = 'InternshipManagementSystem/1.0';

    public function geocode(string $address): array
    {
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($address) {
            $addressVariants = $this->generateAddressVariants($address);
            
            foreach ($addressVariants as $variant) {
                $result = $this->tryGeocode($variant);
                if ($result) {
                    return $result;
                }
            }
            
            throw new \Exception('Address not found');
        });
    }
    
    protected function generateAddressVariants(string $address): array
    {
        $variants = [$address];
        
        $cleanAddress = str_replace(['RT ', 'RW ', 'rt ', 'rw '], '', $address);
        if ($cleanAddress !== $address) {
            $variants[] = $cleanAddress;
        }
        
        $parts = array_map('trim', explode(',', $address));
        if (count($parts) >= 3) {
            $variants[] = implode(', ', array_slice($parts, -3));
            $variants[] = implode(', ', array_slice($parts, -2));
        }
        
        $shortAddress = str_replace(['Jl. ', 'jl. ', 'JL. '], ['Jalan ', 'Jalan ', 'Jalan '], $address);
        if ($shortAddress !== $address) {
            $variants[] = $shortAddress;
        }
        
        return array_unique($variants);
    }
    
    protected function tryGeocode(string $address): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => self::USER_AGENT,
        ])->timeout(10)->get(self::NOMINATIM_URL, [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
            'countrycodes' => 'id',
            'extratags' => 1,
        ]);

        if ($response->successful() && !empty($response->json())) {
            $result = $response->json()[0];
            return [
                'latitude' => (float) $result['lat'],
                'longitude' => (float) $result['lon'],
                'display_name' => $result['display_name'] ?? $address,
                'type' => $result['type'] ?? 'unknown',
            ];
        }

        return null;
    }

    public function geocodeWithRetry(string $address, int $maxRetries = 3): ?array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return $this->geocode($address);
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $maxRetries) {
                    usleep(1000000 * pow(2, $attempt)); // Exponential backoff
                }
            }
        }

        throw $lastException ?? new \Exception('Geocoding failed after ' . $maxRetries . ' attempts');
    }

    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        $cacheKey = 'reverse_' . md5("{$latitude},{$longitude}");
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($latitude, $longitude) {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $result = $response->json();
                return [
                    'display_name' => $result['display_name'] ?? '',
                    'address' => $result['address'] ?? [],
                ];
            }

            return null;
        });
    }
}
