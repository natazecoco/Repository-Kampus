<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\TopicDictionary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AutoTaggingService
{
    private TextPreprocessor $preprocessor;

    public function __construct(TextPreprocessor $preprocessor)
    {
        $this->preprocessor = $preprocessor;
    }

    /**
     * Menganalisis teks publikasi dan menempelkan topik secara otomatis.
     */
    public function tag(Publication $publication): void
    {   
        // [KODE BARU] Ambil dari Database & Cache agar saat tagging massal performanya tetap ngebut!
        $dictionary = Cache::remember('topic_dictionary_mappings', 86400, function () {
            return TopicDictionary::all()
                ->groupBy('target_topic')
                ->map(fn ($items) => $items->pluck('keyword')->toArray())
                ->toArray();
        });
        
        $threshold = config('topic_dictionary.threshold', 10);

        if (empty($dictionary)) {
            return;
        }

        // 1. Bersihkan teks di masing-masing kolom
        $title = $this->preprocessor->process($publication->title ?? '');
        
        $keywordsRaw = is_array($publication->keywords) 
            ? implode(' ', $publication->keywords) 
            : (string) $publication->keywords;
        $keywords = $this->preprocessor->process($keywordsRaw);
        
        $abstract = $this->preprocessor->process($publication->abstract ?? '');

        $matchedTopicNames = [];

        // 2. Evaluasi setiap topik dan sinonimnya
        foreach ($dictionary as $topicName => $synonyms) {
            $score = 0;
            
            $checkWords = array_unique(array_merge([$topicName], $synonyms));

            foreach ($checkWords as $word) {
                $processedWord = $this->preprocessor->process($word);
                if (empty($processedWord)) continue;

                $pattern = '/\b' . preg_quote($processedWord, '/') . '\b/';

                // Pembobotan: Judul (10), Keywords (7), Abstrak (3)
                if (preg_match($pattern, $title)) $score += 10;
                if (preg_match($pattern, $keywords)) $score += 7;
                if (preg_match($pattern, $abstract)) $score += 3;
            }

            // 3. Jika skor mencukupi, masukkan sebagai kandidat topik
            if ($score >= $threshold) {
                $matchedTopicNames[] = $topicName;
            }
        }

        // 4. Cari ID Topik di database dan tarik Parent-nya secara hierarkis (UPDATED)
        $autoTopicIds = [];
        if (!empty($matchedTopicNames)) {
            // Ambil seluruh object modelnya, bukan cuma pluck('id')
            $matchedTopics = Topic::whereIn('name', $matchedTopicNames)
                ->orWhereIn('slug', array_map(fn($name) => Str::slug($name), $matchedTopicNames))
                ->get();

            foreach ($matchedTopics as $topic) {
                // Masukkan ID dari topik yang match
                $autoTopicIds[] = $topic->id;
                
                // Panggil method ancestorIds() buatanmu untuk mengambil seluruh ID parent/kakeknya
                $autoTopicIds = array_merge($autoTopicIds, $topic->ancestorIds());
            }

            // Hapus ID duplikat (misal "React" dan "Laravel" sama-sama punya parent "Web Development")
            $autoTopicIds = array_values(array_unique($autoTopicIds));
        }

        // 5. Simpan ke database dengan aman (Sinkronisasi Pivot)
        $this->syncTopicsSafely($publication, $autoTopicIds);
    }

    /**
     * Memperbarui pivot table tanpa menghapus topik manual pilihan Admin.
     */
    private function syncTopicsSafely(Publication $publication, array $autoTopicIds): void
    {
        // Ambil semua topik yang diinput MANUAL oleh admin (is_auto = false)
        $manualTopicIds = $publication->topics()
            ->wherePivot('is_auto', false)
            ->pluck('topics.id')
            ->toArray();

        $syncData = [];

        // Masukkan kembali topik manual agar tidak hilang
        foreach ($manualTopicIds as $id) {
            $syncData[$id] = ['is_auto' => false];
        }

        // Tambahkan topik otomatis (hanya jika belum ada di list manual)
        foreach ($autoTopicIds as $id) {
            if (!isset($syncData[$id])) {
                $syncData[$id] = ['is_auto' => true];
            }
        }

        // Sync akan menghapus topik lama yang tidak ada di $syncData,
        // tapi mempertahankan topik manual dan menambahkan topik auto yang baru.
        $publication->topics()->sync($syncData);
    }
}