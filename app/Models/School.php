<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'email'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->users()->where('role', 'teacher');
    }

    public function students(): BelongsToMany
    {
        return $this->users()->where('role', 'student');
    }
}
