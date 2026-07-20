<?php

namespace App\Jobs;

use App\Models\Publication;
use App\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class GenerateRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $publication;

    // Menerima data skripsi yang akan dicarikan rekomendasinya
    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    // Fungsi handle() ini adalah tombol "START" dari pekerja belakang layar
    public function handle(): void
    {
        $target = $this->publication;
        $others = Publication::where('id', '!=', $target->id)->get();

        if ($others->isEmpty()) {
            return; // Berhenti jika belum ada skripsi lain di database
        }

        // 1. Inisialisasi Sastrawi (Sekali saja di luar loop)
        $stopWordFactory = new StopWordRemoverFactory();
        $stopword = $stopWordFactory->createStopWordRemover();

        $stemmerFactory = new StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();

        $corpus = [];

        // 2. Pre-processing Target (Dengan Bobot 3x Judul, 2x Keyword, 1x Abstrak)
        $targetTextRaw = $target->title . ' ' . $target->title . ' ' . $target->title . ' ' .
                         $target->keywords . ' ' . $target->keywords . ' ' .
                         $target->abstract;
        $corpus[$target->id] = $this->cleanText($targetTextRaw, $stemmer, $stopword);

        // 3. Pre-processing Pembanding
        foreach ($others as $other) {
            $textRaw = $other->title . ' ' . $other->title . ' ' . $other->title . ' ' .
                       $other->keywords . ' ' . $other->keywords . ' ' .
                       $other->abstract;
            $corpus[$other->id] = $this->cleanText($textRaw, $stemmer, $stopword);
        }

        // 4. Hitung TF & DF
        $df = [];
        $tf = [];
        $N = count($corpus);

        foreach ($corpus as $id => $text) {
            $words = array_filter(explode(' ', $text));
            $totalWords = count($words);
            
            if ($totalWords == 0) continue;

            $wordCounts = array_count_values($words);
            $tf[$id] = [];
            $uniqueWords = array_unique($words);

            foreach ($uniqueWords as $word) {
                $tf[$id][$word] = $wordCounts[$word] / $totalWords;
                if (!isset($df[$word])) $df[$word] = 0;
                $df[$word]++;
            }
        }

        // 5. Hitung IDF & TF-IDF
        $idf = [];
        foreach ($df as $word => $count) {
            $idf[$word] = log($N / $count);
        }

        $tfidf = [];
        foreach ($tf as $id => $wordFreqs) {
            foreach ($wordFreqs as $word => $val) {
                $tfidf[$id][$word] = $val * $idf[$word];
            }
        }

        // 6. Hitung Cosine Similarity
        $targetVector = $tfidf[$target->id] ?? [];
        $similarities = [];

        foreach ($others as $other) {
            $otherVector = $tfidf[$other->id] ?? [];
            $dotProduct = 0;
            $normTarget = 0;
            $normOther = 0;

            $allWords = array_unique(array_merge(array_keys($targetVector), array_keys($otherVector)));

            foreach ($allWords as $word) {
                $valTarget = $targetVector[$word] ?? 0;
                $valOther = $otherVector[$word] ?? 0;

                $dotProduct += ($valTarget * $valOther);
                $normTarget += pow($valTarget, 2);
                $normOther += pow($valOther, 2);
            }

            $normTarget = sqrt($normTarget);
            $normOther = sqrt($normOther);

            $similarity = ($normTarget * $normOther == 0) ? 0 : ($dotProduct / ($normTarget * $normOther));
            $similarities[$other->id] = $similarity;
        }

        // 7. Sorting dan Simpan ke Database
        arsort($similarities);
        $topIds = array_slice(array_keys($similarities), 0, 3); // Top 3

        // Hapus rekomendasi lama untuk skripsi ini agar tidak terjadi duplikasi saat di-update
        Recommendation::where('publication_id', $target->id)->delete();

        // Simpan 3 rekomendasi terbaik yang baru
        foreach ($topIds as $id) {
            if ($similarities[$id] > 0) {
                Recommendation::create([
                    'publication_id' => $target->id,
                    'recommended_id' => $id,
                    'similarity_score' => $similarities[$id],
                ]);
            }
        }
    }

    private function cleanText($text, $stemmer, $stopword)
    {
        if (empty($text)) return '';
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9 ]/', '', $text);
        $text = $stopword->remove($text);
        $text = $stemmer->stem($text);
        return $text;
    }
}