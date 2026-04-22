<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';
    
    protected $fillable = [
        'student_id',
        'internship_id',
        'attendance_date',
        'status',
        'notes',
        'recorded_by',
        'latitude',
        'longitude',
        'location_verified',
        'location_distance',
        'attendance_point_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'location_verified' => 'boolean',
        'location_distance' => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function attendancePoint(): BelongsTo
    {
        return $this->belongsTo(AttendancePoint::class);
    }
}
