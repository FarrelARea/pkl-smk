<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    protected $fillable = [
        'student_id',
        'internship_id',
        'log_date',
        'activities',
        'reflection',
        'teacher_comment',
        'teacher_id',
        'photo',
        'context',
        'latitude',
        'longitude',
        'location_verified',
        'location_distance',
    ];

    protected $casts = [
        'log_date' => 'date',
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

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function canEdit(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24;
    }
}
