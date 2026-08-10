<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Accessor untuk merapikan judul publikasi (Title Case + Special Terms).
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (!$value) return '';

                $specialCases = config('topic_dictionary.title_cases', []);

                $words = explode(' ', strtolower(trim($value)));
                
                foreach ($words as $key => $word) {
                    $cleanWord = trim($word, '(),.!?:"\'');
                    
                    if (array_key_exists($cleanWord, $specialCases)) {
                        $words[$key] = str_replace($cleanWord, $specialCases[$cleanWord], $word);
                    } else {
                        if (str_starts_with($word, '(') && strlen($word) > 1) {
                            $words[$key] = '(' . ucfirst(substr($word, 1));
                        } else {
                            $words[$key] = ucfirst($word);
                        }
                    }
                }

                $firstWordLower = strtolower(trim($words[0] ?? '', '(),.!?:"\''));
                if (array_key_exists($firstWordLower, $specialCases) && !in_array($specialCases[$firstWordLower], ['dan', 'atau', 'di', 'ke', 'dari', 'yang', 'pada', 'untuk', 'dengan', 'dalam', 'terhadap', 'sebagai'])) {
                    $words[0] = str_replace($firstWordLower, $specialCases[$firstWordLower], strtolower($words[0]));
                } else {
                    if (str_starts_with($words[0], '(') && strlen($words[0]) > 1) {
                        $words[0] = '(' . ucfirst(substr($words[0], 1));
                    } else {
                        $words[0] = ucfirst($words[0]);
                    }
                }

                return implode(' ', $words);
            }
        );
    }

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

    public function getMetadataSummaryAttribute(): array
    {
        return [
            'type' => $this->type ?? 'unknown',
            'document_type' => $this->getTypeLabelAttribute(),
            'category' => $this->resolveCategory(),
            'year' => (int) ($this->year ?? 0),
            'has_author' => filled($this->author),
            'has_abstract' => filled($this->abstract),
            'has_keywords' => filled($this->keywords),
        ];
    }

    public function getAdminCompletionStateAttribute(): array
    {
        $requiredSections = self::requiredSectionsForType($this->type ?? null);
        $presentSections = $this->files()->pluck('section')->filter()->values()->all();
        $missingSections = array_values(array_diff($requiredSections, $presentSections));

        $isComplete = empty($missingSections);

        return [
            'is_complete' => $isComplete,
            'status_label' => $isComplete ? 'Lengkap' : 'Perlu Dilengkapi',
            'missing_sections' => $missingSections,
            'required_count' => count($requiredSections),
            'present_count' => count($presentSections),
        ];
    }

    public function resolveCategory(): string
    {
        return match ($this->type) {
            'thesis' => 'Dokumen Akademik',
            'scientific_paper' => 'Penulisan Ilmiah',
            'article' => 'Artikel Jurnal',
            'book' => 'Buku',
            'proceeding' => 'Prosiding',
            'report' => 'Laporan Penelitian',
            default => 'Dokumen',
        };
    }

    public static function typeFilterOptions(): array
    {
        return [
            '' => 'Semua Kategori',
            'thesis' => 'Skripsi / Tesis / Disertasi',
            'scientific_paper' => 'Penulisan Ilmiah',
            'article' => 'Artikel Jurnal',
            'book' => 'Buku',
            'proceeding' => 'Prosiding',
            'report' => 'Laporan Penelitian',
        ];
    }

    public function scopeSearch(Builder $query, ?string $term, array $semanticTerms = []): Builder
    {
        if (empty($term) && empty($semanticTerms)) {
            return $query;
        }

        $term = strtolower(trim((string) $term));

        return $query->where(function (Builder $q) use ($term, $semanticTerms) {
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

            foreach ($semanticTerms as $semanticTerm) {
                $semanticTerm = strtolower(trim((string) $semanticTerm));
                if ($semanticTerm === '') continue;

                $q->orWhereRaw('LOWER(title) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereRaw('LOWER(abstract) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereRaw('LOWER(keywords) LIKE ?', ["%{$semanticTerm}%"])
                  ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->whereRaw('LOWER(name) LIKE ?', ["%{$semanticTerm}%"]));
            }
        })
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
                "%{$term}%", 
                "%{$term}%", 
                "%{$term}%", 
                "%{$term}%", 
            ]);
        })
        ->latest(); 
    }

    public static function requiredSectionsForType(?string $type): array
    {
        $type = $type ?? 'thesis';

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

        $scientificPaperRequired = [
            'cover',
            'originality_statement',
            'abstract_id',
            'table_of_contents',
            'chapter_1',
            'chapter_2',
            'chapter_3',
            'bibliography',
        ];

        $singleDocumentRequired = [
            'full_document',
            'abstract_id',
            'bibliography',
        ];

        return match ($type) {
            'scientific_paper' => $scientificPaperRequired,
            'article', 'book', 'proceeding', 'report' => $singleDocumentRequired,
            'thesis' => $thesisRequired,
            default => $singleDocumentRequired,
        };
    }

    public function missingRequiredSections(): array
    {
        $present = $this->files()->pluck('section')->filter()->values()->all();

        $required = self::requiredSectionsForType($this->type ?? null);

        return array_values(array_diff($required, $present));
    }

    public static function missingRequiredSectionsFromArray(?string $type, array $filesData = []): array
    {
        $present = array_values(array_filter(array_map(fn($f) => $f['section'] ?? null, $filesData)));

        $required = self::requiredSectionsForType($type);

        return array_values(array_diff($required, $present));
    }

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
                return [trim($this->author)];
            }
        }

        return array_values(array_filter(array_map('trim', $authors)));
    }

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

    public function getFormattedAuthorAttribute(): string
    {
        $authors = array_map([$this, 'formatAuthorAPA'], $this->parseAuthors());
        $count = count($authors);

        if ($count === 1) return $authors[0];
        if ($count === 2) return $authors[0] . ' & ' . $authors[1];
        
        $lastAuthor = array_pop($authors);
        return implode(', ', $authors) . ', & ' . $lastAuthor;
    }

    public function getCitation(string $style = 'APA'): string
    {
        $authors = $this->parseAuthors();
        $authorCount = count($authors);
        $year = $this->year ?? date('Y');
        $title = $this->title ?? 'Tanpa Judul'; 
        $container = $this->container ? $this->container->name : 'Universitas Gunadarma';

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

                if ($authorCount === 1) {
                    $authorStr = $formattedAuthors[0];
                } elseif ($authorCount === 2) {
                    $separator = $isIndo ? ' & ' : ' & '; 
                    $authorStr = $formattedAuthors[0] . $separator . $formattedAuthors[1];
                } elseif ($authorCount >= 3 && $authorCount <= 20) {
                    $lastAuthor = array_pop($formattedAuthors);
                    $authorStr = implode(', ', $formattedAuthors) . ', & ' . $lastAuthor;
                } else {
                    $authorStr = $formattedAuthors[0] . ' ' . $etAl;
                }

                if ($this->type === 'article') {
                    return sprintf('%s. (%s). %s. %s.', $authorStr, $year, $title, $container);
                }
                return sprintf('%s. (%s). %s [%s, %s].', $authorStr, $year, $title, $this->type_label, $container);
        }
    }
}