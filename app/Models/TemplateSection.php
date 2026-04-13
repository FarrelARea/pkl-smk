<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSection extends Model
{
    protected $fillable = ['template_id', 'number', 'title', 'order'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class, 'template_id');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(TemplateIndicator::class, 'section_id')->whereNull('parent_id')->orderBy('order');
    }

    public function allIndicators(): HasMany
    {
        return $this->hasMany(TemplateIndicator::class, 'section_id')->orderBy('order');
    }
}
