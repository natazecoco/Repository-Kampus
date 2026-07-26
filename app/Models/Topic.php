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

        // Gather related publication ids
        $publicationIds = $this->publications()->pluck('publications.id')->all();

        // Store backup for potential undo
        \DB::table('topic_merge_backups')->insert([
            'source_id' => $this->id,
            'target_id' => $target->id,
            'publication_ids' => json_encode($publicationIds),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Move relations: attach target and detach source
        foreach ($publicationIds as $pubId) {
            $publication = Publication::find($pubId);
            if (! $publication) continue;

            if (! $publication->topics()->where('topics.id', $target->id)->exists()) {
                $publication->topics()->attach($target->id);
            }

            $publication->topics()->detach($this->id);
        }

        // Mark this topic as merged into target and deactivate it (do not delete)
        $this->merged_into = $target->id;
        $this->is_active = false;
        $this->save();
    }

    /**
     * Undo the latest merge for this topic if any.
     */
    public function undoMerge(): bool
    {
        $backup = \DB::table('topic_merge_backups')
            ->where('source_id', $this->id)
            ->where('undone', false)
            ->orderByDesc('created_at')
            ->first();

        if (! $backup) {
            return false;
        }

        $publicationIds = json_decode($backup->publication_ids, true) ?? [];
        $target = Topic::find($backup->target_id);

        if (! $target) {
            return false;
        }

        // Reattach publications to source and detach from target if needed
        foreach ($publicationIds as $pubId) {
            $publication = Publication::find($pubId);
            if (! $publication) continue;

            if (! $publication->topics()->where('topics.id', $this->id)->exists()) {
                $publication->topics()->attach($this->id);
            }

            // Optionally detach from target only if it wasn't attached before; we can't know original state,
            // so we will not detach from target to avoid data loss. (Admin can clean duplicates later.)
        }

        // Reactivate topic and clear merged_into
        $this->merged_into = null;
        $this->is_active = true;
        $this->save();

        // Mark backup undone
        \DB::table('topic_merge_backups')->where('id', $backup->id)->update(['undone' => true, 'updated_at' => now()]);

        return true;
    }
}
