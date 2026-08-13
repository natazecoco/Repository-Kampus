<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class TextPreprocessor
{
    private $stemmer;
    private $stopword;
    private $whitelist;
    private $topicDictionaryCache;

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopWordFactory = new StopWordRemoverFactory();
        $this->stopword = $stopWordFactory->createStopWordRemover();

        // Load technical whitelist from config (normalize to lowercase)
        $this->whitelist = collect(config('technical_whitelist.terms', []))->map(fn($t) => mb_strtolower($t))->sortByDesc(fn($t) => mb_strlen($t))->values()->all();
        $this->topicDictionaryCache = null;
    }

    /**
     * Membersihkan teks melalui pipeline NLP terpusat.
     */
    public function process(?string $text): string
    {
        if (blank($text)) {
            return '';
        }
        // 1. Case folding (Unicode Safe)
        $cleaned = mb_strtolower(trim($text));

        // 2. Protect multi-word whitelist phrases by joining with a placeholder
        // e.g. "machine learning" -> "machine___learning" so downstream cleaning keeps them intact
        foreach ($this->whitelist as $phrase) {
            if (mb_strpos($phrase, ' ') !== false) {
                $placeholder = str_replace(' ', '___', $phrase);
                $pattern = '/\b' . preg_quote($phrase, '/') . '\b/u';
                $cleaned = preg_replace($pattern, $placeholder, $cleaned);
            }
        }
        
        // 2. Hapus tanda baca/simbol (kecuali placeholder underscore), ganti dengan spasi.
        $cleaned = preg_replace('/[^\p{L}\p{N}\s_]/u', ' ', $cleaned);
        
        // 3. Normalisasi spasi ganda menjadi satu spasi saja
        $cleaned = preg_replace('/\s+/u', ' ', $cleaned);
        $cleaned = trim($cleaned);
        
        // 4. Hapus stopword (kata hubung)
        $cleaned = $this->stopword->remove($cleaned);

        // 5. Optimasi: Hentikan jika teks menjadi kosong setelah stopword dihapus
        if ($cleaned === '') {
            return '';
        }

        // 6. Tokenize dan lakukan alias-first lookup, whitelist-protection, dan stemming selektif
        $tokens = preg_split('/\s+/u', $cleaned, -1, PREG_SPLIT_NO_EMPTY);

        $processed = [];

        foreach ($tokens as $token) {
            // restore whitelist placeholders back to multi-word phrase
            if (mb_strpos($token, '___') !== false) {
                $restored = str_replace('___', ' ', $token);
                $processed[] = $restored;
                continue;
            }

            // skip short tokens and numeric tokens
            if (preg_match('/^\d+$/u', $token) || mb_strlen($token) <= 1) {
                $processed[] = $token;
                continue;
            }

            // 1) Alias-first: check topic dictionary (cached)
            $mapping = $this->lookupTopicDictionary($token);
            if ($mapping) {
                $processed[] = $mapping;
                continue;
            }

            // 2) Whitelist exact token (single-word in whitelist)
            if (in_array($token, $this->whitelist, true)) {
                $processed[] = $token;
                continue;
            }

            // 3) Protect technical tokens: if contains digits or punctuation-like chars, skip stemming
            if (preg_match('/[0-9]|[^a-z0-9_]/u', $token)) {
                $processed[] = $token;
                continue;
            }

            // 4) Apply stemming for remaining tokens
            $stemmed = $this->stemmer->stem($token);
            $processed[] = $stemmed;

            // 5) Log unknown tokens (for curation) when token wasn't mapped and looks like a content word
            if (! $mapping && mb_strlen($token) > 3) {
                $this->logUnknownTerm($token);
            }
        }

        return implode(' ', $processed);
    }

    private function lookupTopicDictionary(string $keyword): ?string
    {
        if ($this->topicDictionaryCache === null) {
            $this->topicDictionaryCache = cache()->rememberForever('topic_dictionary_mappings', function () {
                return \App\Models\TopicDictionary::query()->pluck('target_topic', 'keyword')->map(fn($t) => mb_strtolower($t))->toArray();
            });
        }

        $keyword = mb_strtolower($keyword);

        return $this->topicDictionaryCache[$keyword] ?? null;
    }

    private function logUnknownTerm(string $token): void
    {
        try {
            $path = storage_path('logs/unknown_terms.log');
            $line = '[' . now()->toDateTimeString() . '] ' . $token . PHP_EOL;
            // Use FILE_APPEND to keep history, suppress errors to avoid breaking pipeline
            @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // swallow
        }
    }
}