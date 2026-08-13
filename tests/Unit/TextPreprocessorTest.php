<?php

namespace Tests\Unit;

use App\Models\TopicDictionary;
use App\Services\TextPreprocessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextPreprocessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_whitelist_multi_word_phrases_are_preserved()
    {
        $tp = new TextPreprocessor();

        $input = 'Machine Learning untuk rekomendasi';
        $out = $tp->process($input);

        $this->assertStringContainsString('machine learning', $out);
    }

    public function test_topic_dictionary_alias_is_applied_before_stemming()
    {
        // Insert mapping ai -> artificial intelligence
        TopicDictionary::create(['keyword' => 'ai', 'target_topic' => 'artificial intelligence']);

        $tp = new TextPreprocessor();

        $input = 'AI dan sistem';
        $out = $tp->process($input);

        $this->assertStringContainsString('artificial intelligence', $out);
    }
}
