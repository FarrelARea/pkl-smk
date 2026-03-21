<?php

namespace App\Services;

class DistanceService
{
    protected const EARTH_RADIUS_METERS = 6371000;

    public function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    public function validateLocation(
        float $studentLat,
        float $studentLon,
        float $companyLat,
        float $companyLon,
        float $thresholdMeters = 100
    ): array {
        $distance = $this->calculateDistance(
            $studentLat, $studentLon,
            $companyLat, $companyLon
        );

        return [
            'verified' => $distance <= $thresholdMeters,
            'distance' => round($distance, 2),
            'threshold' => $thresholdMeters,
            'message' => $distance <= $thresholdMeters
                ? 'Location verified'
                : "Location outside threshold ({$distance}m from company)",
        ];
    }

    public function getDistanceInMeters(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        return $this->calculateDistance($lat1, $lon1, $lat2, $lon2);
    }

    public function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters, 1) . ' m';
        }
        
        return round($meters / 1000, 2) . ' km';
    }
}
