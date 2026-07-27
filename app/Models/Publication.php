<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Jobs\GenerateRecommendations;

class Publication extends Model
{
    protected $guarded = [];

    public const TYPE_LABELS = [
        'thesis' => 'Skripsi / Tesis / Disertasi',
        'scientific_paper' => 'Penulisan Ilmiah',
        'article' => 'Artikel Jurnal',
        'book' => 'Buku',
        'proceeding' => 'Prosiding',
        'report' => 'Laporan Penelitian',
    ];

    // Relasi: Banyak Publication dimiliki oleh Satu Container
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    // Fungsi ini akan otomatis berjalan saat ada kejadian (event) di database
    protected static function booted()
    {
        // Berjalan SATU KALI saat ada skripsi BARU ditambah
        static::created(function ($publication) {
            GenerateRecommendations::dispatch($publication);
        });

        // Berjalan setiap kali skripsi DIEDIT (misal judulnya direvisi)
        static::updated(function ($publication) {
            GenerateRecommendations::dispatch($publication);
        });
    }

    public function files()
    {
        return $this->hasMany(PublicationFile::class)->orderBy('sort_order');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst((string) $this->type);
    }

    /**
     * Return list of required section keys for a given publication type.
     * These reflect reasonable defaults based on typical campus repository templates.
     *
     * @return array<int, string>
     */
    public static function requiredSectionsForType(?string $type): array
    {
        $type = $type ?? 'thesis';

        // Defaults inspired by the list you provided (bintang = wajib)
        $thesisRequired = [
            'cover',
            'originality_statement',
            'approval_sheet',
            'abstract_id',
            'table_of_contents',
            'chapter_1',
            'chapter_2',
            'chapter_3',
            'bibliography',
            'presentation',
        ];

        switch ($type) {
            case 'scientific_paper':
                // Penulisan ilmiah: mirip skripsi tapi lebih ringkas
                return $thesisRequired;

            case 'article':
            case 'book':
            case 'proceeding':
            case 'report':
                // Dokumen tunggal: minimal dokumen lengkap dan daftar pustaka
                return [
                    'full_document',
                    'abstract_id',
                    'bibliography',
                ];

            case 'thesis':
            default:
                return $thesisRequired;
        }
    }

    /**
     * Return missing required section keys for this publication using persisted files.
     *
     * @return array<int, string>
     */
    public function missingRequiredSections(): array
    {
        $present = $this->files()->pluck('section')->filter()->values()->all();

        $required = self::requiredSectionsForType($this->type ?? null);

        return array_values(array_diff($required, $present));
    }

    /**
     * Same as missingRequiredSections but based on incoming form data array (files as array).
     * Useful when validating form state before the record is persisted.
     *
     * @param  array<int, array<string,mixed>>  $filesData
     * @return array<int, string>
     */
    public static function missingRequiredSectionsFromArray(?string $type, array $filesData = []): array
    {
        $present = array_values(array_filter(array_map(fn($f) => $f['section'] ?? null, $filesData)));

        $required = self::requiredSectionsForType($type);

        return array_values(array_diff($required, $present));
    }
}

