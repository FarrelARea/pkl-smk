<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLogComment extends Model
{
    public $timestamps = false;

    protected $fillable = ['daily_log_id', 'author_id', 'author_role', 'comment'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class, 'daily_log_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
