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
        'merged_at' => 'datetime',
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

    public function ancestorIds(): array
    {
        $ids = [];
        $current = $this->parent()->with('parent')->first();

        while ($current) {
            if (in_array($current->id, $ids, true)) {
                break;
            }

            $ids[] = $current->id;
            $current = $current->parent()->with('parent')->first();
        }

        return array_values(array_unique($ids));
    }

    public function relatedTopicIds(): array
    {
        $ids = [$this->id];

        foreach ($this->ancestorIds() as $ancestorId) {
            $ids[] = $ancestorId;
        }

        foreach ($this->children()->pluck('id') as $childId) {
            $ids[] = $childId;
        }

        return array_values(array_unique($ids));
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class)->withPivot('is_auto');
    }

    public function userPreferences()
    {
        return $this->hasMany(UserTopicPreference::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_topic_preferences');
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

        // Determine which publications will be newly attached to target
        $added = [];

        foreach ($publicationIds as $pubId) {
            $publication = Publication::find($pubId);
            if (! $publication) continue;

            $already = $publication->topics()->where('topics.id', $target->id)->exists();
            if (! $already) {
                $publication->topics()->attach($target->id);
                $added[] = $pubId;
            }

            // Detach this topic from publication
            $publication->topics()->detach($this->id);
        }

        // Determine actor
        $actorId = auth()->id() ?? null;

        // Store backup for potential undo, including list of added attachments and audit info
        \DB::table('topic_merge_backups')->insert([
            'source_id' => $this->id,
            'target_id' => $target->id,
            'publication_ids' => json_encode($publicationIds),
            'added_publication_ids' => json_encode($added),
            'merged_by' => $actorId,
            'merged_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark this topic as merged into target and deactivate it (do not delete)
        $this->merged_into = $target->id;
        $this->merged_by = $actorId;
        $this->merged_at = now();
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
        $added = json_decode($backup->added_publication_ids ?? '[]', true) ?? [];
        $target = Topic::find($backup->target_id);

        if (! $target) {
            return false;
        }

        // Reattach publications to source and detach from target only for those that were newly added
        foreach ($publicationIds as $pubId) {
            $publication = Publication::find($pubId);
            if (! $publication) continue;

            // Reattach to source if missing
            if (! $publication->topics()->where('topics.id', $this->id)->exists()) {
                $publication->topics()->attach($this->id);
            }

            // Detach from target only if it was added by the merge
            if (in_array($pubId, $added, true)) {
                $publication->topics()->detach($target->id);
            }
        }

        // Reactivate topic and clear merged_into and audit fields
        $this->merged_into = null;
        $this->merged_by = null;
        $this->merged_at = null;
        $this->is_active = true;
        $this->save();

        // Mark backup undone
        \DB::table('topic_merge_backups')->where('id', $backup->id)->update(['undone' => true, 'updated_at' => now()]);

        return true;
    }
}
