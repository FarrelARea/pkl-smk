<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClockInOut extends Model
{
    protected $fillable = [
        'user_id',
        'internship_id',
        'type',
        'latitude',
        'longitude',
        'company_latitude',
        'company_longitude',
        'distance_meters',
        'is_within_range',
        'notes',
        'photo',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'company_latitude' => 'decimal:8',
        'company_longitude' => 'decimal:8',
        'distance_meters' => 'decimal:2',
        'is_within_range' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
