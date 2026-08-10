<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ResearchMethodDictionary extends Model
{
    protected $fillable = [
        'method_name',
        'aliases',
        'description',
        'category',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget('research_method_dictionary'));
        static::deleted(fn (): bool => Cache::forget('research_method_dictionary'));
    }
}
