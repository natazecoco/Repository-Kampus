<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class PublicationFile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allow_download' => 'boolean',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function canBeViewedBy(?Authenticatable $user): bool
    {
        if ($user?->role === 'admin') {
            return true;
        }

        $effectiveVisibility = $this->visibility ?? ($this->access_type === 'public' ? 'public' : 'authenticated');

        return match ($effectiveVisibility) {
            'public' => true,
            'authenticated' => $user !== null,
            'admin' => $user?->role === 'admin',
            default => false,
        };
    }

    public function canBeDownloadedBy(?Authenticatable $user): bool
    {
        if ($user?->role === 'admin') {
            return true;
        }

        return $this->allow_download && $this->canBeViewedBy($user);
    }

    public function isPublic(): bool
    {
        $effectiveVisibility = $this->visibility ?? ($this->access_type === 'public' ? 'public' : 'authenticated');
        return $effectiveVisibility === 'public';
    }

    public function isRestricted(): bool
    {
        return ! $this->isPublic();
    }

    public function getVisibilityLabelAttribute(): string
    {
        $effectiveVisibility = $this->visibility ?? ($this->access_type === 'public' ? 'public' : 'authenticated');

        return match ($effectiveVisibility) {
            'public' => 'Publik',
            'authenticated' => 'Mahasiswa internal',
            'admin' => 'Admin saja',
            default => 'Terbatas',
        };
    }
}