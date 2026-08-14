<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAssessment extends Model
{
    protected $fillable = [
        'student_id', 'internship_id', 'teacher_id', 'last_updated_by_id',
        'template_snapshot', 'teacher_notes', 'teacher_checklist', 'status',
    ];

    protected $casts = [
        'template_snapshot' => 'array',
        'teacher_checklist' => 'array',
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

    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'student_assessment_id');
    }
}
