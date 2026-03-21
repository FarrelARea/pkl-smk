<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'internship_id',
        'parent_id',
        'title',
        'file_path',
        'file_type',
        'status',
        'teacher_note',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StudentDocument::class, 'parent_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'parent_id');
    }
}
