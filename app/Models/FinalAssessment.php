<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalAssessment extends Model
{
    protected $fillable = ['student_id', 'internship_id', 'school_admin_id', 'final_score', 'comments', 'grade'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function schoolAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'school_admin_id');
    }
}
