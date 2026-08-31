<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'review_status',
        'review_note',
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

    public function comments(): HasMany
    {
        return $this->hasMany(DailyLogComment::class)->orderBy('created_at');
    }

    public function canEdit(): bool
    {
        if ($this->review_status === 'approved') return false;
        // Only today's logs editable; past dates locked
        return $this->log_date->isToday();
    }

    public function isApproved(): bool
    {
        return $this->review_status === 'approved';
    }

    public function needsRevision(): bool
    {
        return $this->review_status === 'needs_revision';
    }
}
