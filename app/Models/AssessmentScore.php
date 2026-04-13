<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentScore extends Model
{
    protected $fillable = [
        'student_assessment_id', 'section_number', 'indicator_number',
        'indicator_description', 'score', 'notes', 'is_additional',
    ];

    protected $casts = [
        'is_additional' => 'boolean',
    ];

    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class, 'student_assessment_id');
    }
}
