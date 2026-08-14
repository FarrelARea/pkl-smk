<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    protected $fillable = [
        'student_id',
        'internship_id',
        'request_date',
        'end_date',
        'type',
        'reason',
        'document',
        'status',
        'handled_by',
        'handler_note',
        'handled_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'end_date' => 'date',
        'handled_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(int $handlerId, ?string $note = null): void
    {
        $this->update([
            'status' => 'approved',
            'handled_by' => $handlerId,
            'handler_note' => $note,
            'handled_at' => now(),
        ]);
    }

    public function reject(int $handlerId, ?string $note = null): void
    {
        $this->update([
            'status' => 'rejected',
            'handled_by' => $handlerId,
            'handler_note' => $note,
            'handled_at' => now(),
        ]);
    }
}
