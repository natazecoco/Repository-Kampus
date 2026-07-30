<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function files()
    {
        return $this->hasMany(PublicationFile::class)->orderBy('sort_order');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class)->withPivot('is_auto');
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
     * Scope untuk Weighted Search (Bobot: Judul > Keyword > Author > Abstrak)
     */
    public function scopeSearch(Builder $query, ?string $term, array $semanticTerms = []): Builder
    {
        if (empty($term) && empty($semanticTerms)) {
            return $query;
        }

        $term = strtolower(trim((string) $term));

        return $query->where(function (Builder $q) use ($term, $semanticTerms) {
            // Pencarian keyword utama
            if ($term !== '') {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(keywords) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(abstract) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(author) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('year LIKE ?', ["%{$term}%"])
                  ->orWhereHas('container', function ($containerQuery) use ($term) {
                      $containerQuery->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                                     ->orWhereRaw('LOWER(identifier) LIKE ?', ["%{$term}%"]);
                  })
                  ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]));
            }

            // Pencarian perluasan sinonim dan topik (dari expandSearchTerms)
            foreach ($semanticTerms as $semanticTerm) {
                $semanticTerm = strtolower(trim((string) $semanticTerm));
                if ($semanticTerm === '') continue;

                $q->orWhereRaw('LOWER(title) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereRaw('LOWER(abstract) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereRaw('LOWER(keywords) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->whereRaw('LOWER(name) LIKE ?', ["%{$semanticTerm}%"]));
            }
        })
        // Pengurutan bobot kecocokan keyword utama menggunakan CASE statement
        ->when($term !== '', function (Builder $q) use ($term) {
            $q->orderByRaw("
                CASE 
                    WHEN LOWER(title) LIKE ? THEN 4
                    WHEN LOWER(keywords) LIKE ? THEN 3
                    WHEN LOWER(author) LIKE ? THEN 2
                    WHEN LOWER(abstract) LIKE ? THEN 1
                    ELSE 0 
                END DESC
            ", [
                "%{$term}%", // Bobot 4 (Judul)
                "%{$term}%", // Bobot 3 (Keyword)
                "%{$term}%", // Bobot 2 (Author)
                "%{$term}%", // Bobot 1 (Abstrak)
            ]);
        })
        ->latest(); // Fallback urutan berdasarkan dokumen terbaru
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

    /**
     * Helper: Parse string author menjadi array nama-nama bersih.
     * Mendukung pemisah titik koma (;), kata 'dan', 'and', atau koma untuk banyak penulis.
     */
    protected function parseAuthors(): array
    {
        if (empty($this->author)) {
            return ['Anonim'];
        }

        if (str_contains($this->author, ';')) {
            $authors = explode(';', $this->author);
        } elseif (str_contains(strtolower($this->author), ' dan ')) {
            $authors = preg_split('/[\s]+dan[\s]+/i', $this->author);
        } elseif (str_contains(strtolower($this->author), ' and ')) {
            $authors = preg_split('/[\s]+and[\s]+/i', $this->author);
        } else {
            $authors = explode(',', $this->author);
            if (count($authors) === 2 && strlen(trim($authors[1])) <= 6) {
                // Anggap gelar pendek, misal "Nama, S.Kom" -> 1 author
                return [trim($this->author)];
            }
        }

        return array_values(array_filter(array_map('trim', $authors)));
    }

    /**
     * Helper: Format single author untuk APA & Harvard -> "Nama Belakang, Inisial."
     */
    protected function formatAuthorAPA(string $fullName): string
    {
        $cleanName = preg_replace('/,?\s*(S\.Kom|M\.Kom|S\.T|M\.T|Ph\.D|Dr\.|Prof\.).*/i', '', $fullName);
        $parts = explode(' ', trim($cleanName));
        
        if (count($parts) === 1) {
            return $parts[0];
        }

        $lastName = array_pop($parts);
        $initials = '';
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1)) . '. ';
            }
        }

        return $lastName . ', ' . trim($initials);
    }

    /**
     * [BARU - FASE 2A] Mengubah format author menjadi format sitasi single/multi author default.
     */
    public function getFormattedAuthorAttribute(): string
    {
        $authors = array_map([$this, 'formatAuthorAPA'], $this->parseAuthors());
        $count = count($authors);

        if ($count === 1) return $authors[0];
        if ($count === 2) return $authors[0] . ' & ' . $authors[1];
        
        $lastAuthor = array_pop($authors);
        return implode(', ', $authors) . ', & ' . $lastAuthor;
    }

    /**
     * [BARU - FASE 2A PLUS BILINGUAL] Generate format sitasi pintar (APA, IEEE, Harvard) 
     * dengan dukungan Multi-Author & Auto-Detect Bahasa (dkk. / et al.)
     */
    public function getCitation(string $style = 'APA'): string
    {
        $authors = $this->parseAuthors();
        $authorCount = count($authors);
        $year = $this->year ?? date('Y');
        $title = $this->title ?? 'Tanpa Judul';
        $container = $this->container ? $this->container->name : 'Universitas Gunadarma';

        // Cek bahasa dokumen (default ke 'id' / Indonesia kalau kosong)
        $isIndo = !isset($this->language) || in_array(strtolower(trim($this->language)), ['id', 'indonesia', 'in']);
        $etAl = $isIndo ? 'dkk.' : 'et al.';
        $andWord = $isIndo ? 'dan' : 'and';

        switch (strtoupper($style)) {
            case 'IEEE':
                $formatIEEE = function($fullName) {
                    $cleanName = preg_replace('/,?\s*(S\.Kom|M\.Kom|S\.T|M\.T|Ph\.D|Dr\.|Prof\.).*/i', '', $fullName);
                    $parts = explode(' ', trim($cleanName));
                    if (count($parts) === 1) return $parts[0];
                    $lastName = array_pop($parts);
                    $initials = '';
                    foreach ($parts as $part) {
                        if (!empty($part)) $initials .= strtoupper(substr($part, 0, 1)) . '. ';
                    }
                    return trim($initials) . ' ' . $lastName;
                };

                // IEEE: > 6 penulis langsung dipangkas
                if ($authorCount > 6) {
                    $authorStr = $formatIEEE($authors[0]) . ' ' . $etAl;
                } elseif ($authorCount === 1) {
                    $authorStr = $formatIEEE($authors[0]);
                } elseif ($authorCount === 2) {
                    $authorStr = $formatIEEE($authors[0]) . ' ' . $andWord . ' ' . $formatIEEE($authors[1]);
                } else {
                    $lastAuthor = $formatIEEE(array_pop($authors));
                    $authorStr = implode(', ', array_map($formatIEEE, $authors)) . ', ' . $andWord . ' ' . $lastAuthor;
                }

                if ($this->type === 'article') {
                    return sprintf('%s, "%s," %s, %s.', $authorStr, $title, $container, $year);
                }
                return sprintf('%s, "%s," %s, %s, %s.', $authorStr, $title, $this->type_label, $container, $year);

            case 'HARVARD':
                $formattedAuthors = array_map([$this, 'formatAuthorAPA'], $authors);

                // Harvard: > 3 penulis dipangkas
                if ($authorCount === 1) {
                    $authorStr = $formattedAuthors[0];
                } elseif ($authorCount === 2) {
                    $authorStr = $formattedAuthors[0] . ' ' . $andWord . ' ' . $formattedAuthors[1];
                } elseif ($authorCount === 3) {
                    $lastAuthor = array_pop($formattedAuthors);
                    $authorStr = implode(', ', $formattedAuthors) . ' ' . $andWord . ' ' . $lastAuthor;
                } else {
                    $authorStr = $formattedAuthors[0] . ' ' . $etAl;
                }

                if ($this->type === 'article') {
                    return sprintf('%s (%s) \'%s\', %s.', $authorStr, $year, $title, $container);
                }
                return sprintf('%s (%s) %s. %s: %s.', $authorStr, $year, $title, $this->type_label, $container);

            case 'APA':
            default:
                $formattedAuthors = array_map([$this, 'formatAuthorAPA'], $authors);

                // APA 7th: Toleran sampai 20 penulis
                if ($authorCount === 1) {
                    $authorStr = $formattedAuthors[0];
                } elseif ($authorCount === 2) {
                    // Di Indonesia, APA sering tetap pakai '&' atau 'dan' tergantung pedoman kampus
                    $separator = $isIndo ? ' & ' : ' & '; 
                    $authorStr = $formattedAuthors[0] . $separator . $formattedAuthors[1];
                } elseif ($authorCount >= 3 && $authorCount <= 20) {
                    $lastAuthor = array_pop($formattedAuthors);
                    $authorStr = implode(', ', $formattedAuthors) . ', & ' . $lastAuthor;
                } else {
                    // APA 7th untuk > 20 penulis -> Penulis pertama dkk. / et al.
                    $authorStr = $formattedAuthors[0] . ' ' . $etAl;
                }

                if ($this->type === 'article') {
                    return sprintf('%s. (%s). %s. %s.', $authorStr, $year, $title, $container);
                }
                return sprintf('%s. (%s). %s [%s, %s].', $authorStr, $year, $title, $this->type_label, $container);
        }
    }
}