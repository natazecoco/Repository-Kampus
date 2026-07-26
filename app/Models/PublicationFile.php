<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class PublicationFile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'allow_download' => 'boolean',
        ];
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function canBeViewedBy(?Authenticatable $user): bool
    {
        if ($user?->role === 'admin') {
            return true;
        }

        return match ($this->visibility ?? ($this->access_type === 'public' ? 'public' : 'authenticated')) {
            'public' => true,
            'authenticated' => $user !== null,
            default => false,
        };
    }

    public function canBeDownloadedBy(?Authenticatable $user): bool
    {
        return $this->allow_download && $this->canBeViewedBy($user);
    }

    public function getVisibilityLabelAttribute(): string
    {
        return match ($this->visibility ?? 'authenticated') {
            'public' => 'Publik',
            'authenticated' => 'Mahasiswa internal',
            'admin' => 'Admin saja',
            default => 'Terbatas',
        };
    }
}