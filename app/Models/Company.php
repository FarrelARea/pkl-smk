<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'industry',
        'phone',
        'email',
        'latitude',
        'longitude',
        'distance_threshold',
        'province_name',
        'city_name',
        'district_name',
        'village_name',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_threshold' => 'integer',
    ];

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->village_name,
            $this->district_name,
            $this->city_name,
            $this->province_name,
            'Indonesia',
        ]);
        
        return implode(', ', $parts);
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(User::class, 'company_id')->where('role', 'company_supervisor');
    }

    public function attendancePoints(): HasMany
    {
        return $this->hasMany(AttendancePoint::class);
    }

    public function assignedTeachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teacher_company_assignments', 'company_id', 'teacher_id')
            ->withTimestamps();
    }
}
