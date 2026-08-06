<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use App\Services\TextPreprocessor;

class RecommendationScorer
{
    private TextPreprocessor $preprocessor;

    public function __construct(TextPreprocessor $preprocessor)
    {
        $this->preprocessor = $preprocessor;
    }

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

        $documents = collect();
        
        $documents->put($target->id, $this->prepareText($target));

        foreach ($candidateCollection as $candidate) {
            $documents->put($candidate->id, $this->prepareText($candidate));
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

        // Batas maksimal skor rasio
        // textSimilarity (maks 1.0) + knowledgeBonus (maks ~0.55) + userPreferenceBonus (maks ~0.54) = ~2.09
        $maxPossibleScore = 1.0 + 0.55 + ($user ? 0.54 : 0.0);

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

            $rawScore = $textSimilarity + $knowledgeBonus;

            if ($user) {
                $rawScore += $this->userPreferenceBonus($user, $candidate, $candidateTopicIds);
                $rawScore += $this->bookmarkBehaviorBonus($user, $candidate);
            }

            $rawScore += $this->documentTypeBonus($target, $candidate);
            $rawScore += $this->recencyBonus($candidate);
            $rawScore += $this->accessibilityBonus($candidate);

            // [PERBAIKAN] Normalisasi akhir agar skor selalu dicap dalam batas 0.0 sampai 1.0 (0 - 100%)
            $finalScore = min(1.0, max(0.0, $rawScore / $maxPossibleScore));

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

    private function prepareText(Publication $publication): string
    {
        return $this->preprocessor->process($this->getRawText($publication));
    }

    private function getRawText(Publication $publication): string
    {
        $keywords = is_array($publication->keywords) 
            ? implode(' ', $publication->keywords) 
            : (string) $publication->keywords;

        return trim(implode(' ', array_filter([
            $publication->title ?? '',
            $keywords,
            $publication->abstract ?? '',
        ])));
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

    private function documentTypeBonus(Publication $target, Publication $candidate): float
    {
        if ($target->type === null || $candidate->type === null) {
            return 0.0;
        }

        return $target->type === $candidate->type ? 0.08 : 0.0;
    }

    private function recencyBonus(Publication $candidate): float
    {
        $year = (int) ($candidate->year ?? 0);

        if ($year <= 0) {
            return 0.0;
        }

        if ($year >= now()->year) {
            return 0.05;
        }

        return max(0.0, 0.03 - (($year < now()->year) ? 0.002 * (now()->year - $year) : 0.0));
    }

    private function bookmarkBehaviorBonus(User $user, Publication $candidate): float
    {
        $bookmarkedIds = $user->bookmarks()->pluck('publication_id')->all();
        if ($bookmarkedIds === []) {
            return 0.0;
        }

        $sharedBookmarks = array_intersect($bookmarkedIds, [$candidate->id]);
        if ($sharedBookmarks === []) {
            return 0.0;
        }

        return 0.05;
    }

    private function accessibilityBonus(Publication $candidate): float
    {
        $hasFiles = $candidate->files()->exists();
        if (! $hasFiles) {
            return 0.0;
        }

        return 0.03;
    }

    private function expandTopicContextIds(array $topicIds): array
    {
        if (empty($topicIds)) {
            return [];
        }

        $topics = Topic::whereIn('id', $topicIds)->with(['parent.parent', 'children'])->get();
        $contextIds = [];

        foreach ($topics as $topic) {
            $contextIds[] = $topic->id;
            
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