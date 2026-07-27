<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class RecommendationScorer
{
    /**
     * Menghitung skor kemiripan kandidat publikasi terhadap target.
     * Mengembalikan array terstruktur yang memuat skor teks dan irisan taksonomi.
     */
    public function scoreCandidates(Publication $target, iterable $candidates, ?User $user = null): array
    {
        $candidateCollection = collect($candidates)->filter(fn ($candidate) => $candidate instanceof Publication && $candidate->id !== $target->id);

        if ($candidateCollection->isEmpty()) {
            return [];
        }

        $stopWordFactory = new StopWordRemoverFactory();
        $stopword = $stopWordFactory->createStopWordRemover();

        $stemmerFactory = new StemmerFactory();
        $stemmer = $stemmerFactory->createStemmer();

        $documents = collect();
        $documents->put($target->id, $this->prepareText($target, $stemmer, $stopword));

        foreach ($candidateCollection as $candidate) {
            $documents->put($candidate->id, $this->prepareText($candidate, $stemmer, $stopword));
        }

        $df = [];
        $tf = [];
        $documentIds = $documents->keys()->all();
        $N = count($documentIds);

        foreach ($documents as $documentId => $text) {
            $words = array_filter(explode(' ', $text));
            $totalWords = count($words);

            if ($totalWords === 0) {
                continue;
            }

            $wordCounts = array_count_values($words);
            $tf[$documentId] = [];
            $uniqueWords = array_unique($words);

            foreach ($uniqueWords as $word) {
                $tf[$documentId][$word] = $wordCounts[$word] / $totalWords;
                $df[$word] = ($df[$word] ?? 0) + 1;
            }
        }

        $idf = [];
        foreach ($df as $word => $count) {
            $idf[$word] = log($N / $count);
        }

        $tfidf = [];
        foreach ($tf as $documentId => $wordFreqs) {
            foreach ($wordFreqs as $word => $value) {
                $tfidf[$documentId][$word] = $value * ($idf[$word] ?? 0);
            }
        }

        $targetVector = $tfidf[$target->id] ?? [];
        $scores = [];

        // Preload topik target dan konteksnya untuk menghindari query berulang
        $targetTopicIds = $target->topics()->pluck('topics.id')->all();
        $targetContext = $this->expandTopicContextIds($targetTopicIds);

        foreach ($candidateCollection as $candidate) {
            $candidateVector = $tfidf[$candidate->id] ?? [];
            $dotProduct = 0;
            $normTarget = 0;
            $normCandidate = 0;

            $allWords = array_unique(array_merge(array_keys($targetVector), array_keys($candidateVector)));
            foreach ($allWords as $word) {
                $targetValue = $targetVector[$word] ?? 0;
                $candidateValue = $candidateVector[$word] ?? 0;

                $dotProduct += $targetValue * $candidateValue;
                $normTarget += pow($targetValue, 2);
                $normCandidate += pow($candidateValue, 2);
            }

            $normTarget = sqrt($normTarget);
            $normCandidate = sqrt($normCandidate);

            $textSimilarity = ($normTarget * $normCandidate === 0) ? 0 : $dotProduct / ($normTarget * $normCandidate);
            
            // Hitung Knowledge Overlap menggunakan context yang sudah di-expand
            $candidateTopicIds = $candidate->topics()->pluck('topics.id')->all();
            $candidateContext = $this->expandTopicContextIds($candidateTopicIds);
            $knowledgeOverlap = count(array_intersect($targetContext, $candidateContext));
            
            $knowledgeBonus = 0;
            if ($knowledgeOverlap > 0) {
                $knowledgeBonus = 0.25 + min($knowledgeOverlap, 3) * 0.1;
            }

            $finalScore = $textSimilarity + $knowledgeBonus;

            if ($user) {
                $finalScore += $this->userPreferenceBonus($user, $candidate, $candidateTopicIds);
            }

            $scores[] = [
                'publication' => $candidate,
                'score' => round($finalScore, 4),
                'text_similarity' => round($textSimilarity, 4),
                'knowledge_overlap' => $knowledgeOverlap,
            ];
        }

        usort($scores, fn ($left, $right) => $right['score'] <=> $left['score']);

        return $scores;
    }

    public function knowledgeOverlap(Publication $target, Publication $candidate): int
    {
        $targetTopics = $target->topics()->pluck('topics.id')->all();
        $candidateTopics = $candidate->topics()->pluck('topics.id')->all();

        $targetContext = $this->expandTopicContextIds($targetTopics);
        $candidateContext = $this->expandTopicContextIds($candidateTopics);

        return count(array_intersect($targetContext, $candidateContext));
    }

    private function prepareText(Publication $publication, $stemmer, $stopword): string
    {
        $rawText = trim(implode(' ', array_filter([
            $publication->title,
            $publication->keywords,
            $publication->abstract,
        ])));

        if ($rawText === '') {
            return '';
        }

        $text = strtolower($rawText);
        $text = preg_replace('/[^a-z0-9 ]/', '', $text);
        $text = $stopword->remove($text);

        return $stemmer->stem($text);
    }

    private function userPreferenceBonus(User $user, Publication $candidate, ?array $candidateTopicIds = null): float
    {
        $preferenceTopicIds = $user->topicPreferences()->pluck('topic_id')->all();
        if ($preferenceTopicIds === []) {
            return 0.0;
        }

        $candidateTopicIds = $candidateTopicIds ?? $candidate->topics()->pluck('topics.id')->all();
        $sharedPreferences = array_intersect($preferenceTopicIds, $candidateTopicIds);

        if ($sharedPreferences === []) {
            return 0.0;
        }

        return min(count($sharedPreferences) * 0.18, 0.54);
    }

    private function expandTopicContextIds(array $topicIds): array
    {
        if (empty($topicIds)) {
            return [];
        }

        // Optimasi: Tarik seluruh topik terkait sekaligus untuk mencegah N+1 query problem
        $topics = Topic::whereIn('id', $topicIds)->with(['parent.parent', 'children'])->get();
        $contextIds = [];

        foreach ($topics as $topic) {
            $contextIds[] = $topic->id;
            
            // Masukkan ID dari relasi relatedTopicIds secara aman
            $contextIds = array_merge($contextIds, [$topic->id]);
            if ($topic->parent_id) {
                $contextIds[] = $topic->parent_id;
            }
            foreach ($topic->children as $child) {
                $contextIds[] = $child->id;
            }
        }

        return array_values(array_unique(array_filter($contextIds)));
    }
}