<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateIndicator extends Model
{
    protected $fillable = ['section_id', 'parent_id', 'number', 'description', 'order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(TemplateSection::class, 'section_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TemplateIndicator::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TemplateIndicator::class, 'parent_id')->orderBy('order');
    }
}
