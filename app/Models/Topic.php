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

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

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
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Admin helpers
    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Merge this topic into another topic.
     * Moves publications relations to the $target and deletes this topic.
     */
    public function mergeInto(Topic $target): void
    {
        if ($this->id === $target->id) {
            return;
        }

        // Move each related publication to target
        $publicationIds = $this->publications()->pluck('publications.id')->all();

        foreach ($publicationIds as $pubId) {
            $publication = Publication::find($pubId);
            if (! $publication) {
                continue;
            }

            // Attach target topic if not already attached
            if (! $publication->topics()->where('topics.id', $target->id)->exists()) {
                $publication->topics()->attach($target->id);
            }

            // Detach this topic from publication
            $publication->topics()->detach($this->id);
        }

        // Finally delete this topic
        $this->delete();
    }
}
