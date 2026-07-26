<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Topic extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Topic $topic): void {
            if (blank($topic->slug)) {
                $topic->slug = Str::slug($topic->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class);
    }
}
