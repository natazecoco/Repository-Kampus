<?php

namespace Tests\Unit;

use App\Models\TopicDictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TopicDictionaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', ['--path' => 'database/migrations/2026_08_05_054421_create_topic_dictionaries_table.php']);
    }

    public function test_saving_topic_dictionary_forgets_cached_mappings(): void
    {
        Cache::put('topic_dictionary_mappings', ['stale' => ['old']], 60);

        TopicDictionary::create([
            'keyword' => 'machine learning',
            'target_topic' => 'Artificial Intelligence',
        ]);

        $this->assertFalse(Cache::has('topic_dictionary_mappings'));
    }
}
