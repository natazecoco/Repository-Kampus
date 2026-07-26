<?php

namespace Tests\Unit;

use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_merge_moves_publications_and_deletes_source()
    {
        $container = \App\Models\Container::create(['name' => 'Test', 'type' => 'university']);

        $p1 = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'P1',
            'author' => 'A',
            'year' => 2026,
            'abstract' => 'x',
            'keywords' => 'x',
        ]);

        $p2 = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'P2',
            'author' => 'B',
            'year' => 2026,
            'abstract' => 'y',
            'keywords' => 'y',
        ]);

        $source = Topic::create(['name' => 'Source', 'slug' => 'source-topic']);
        $target = Topic::create(['name' => 'Target', 'slug' => 'target-topic']);

        $p1->topics()->attach($source->id);
        $p2->topics()->attach($source->id);

        $this->assertDatabaseHas('publication_topic', ['publication_id' => $p1->id, 'topic_id' => $source->id]);

        $source->mergeInto($target);

        $this->assertDatabaseMissing('topics', ['id' => $source->id]);
        $this->assertDatabaseHas('publication_topic', ['publication_id' => $p1->id, 'topic_id' => $target->id]);
        $this->assertDatabaseHas('publication_topic', ['publication_id' => $p2->id, 'topic_id' => $target->id]);
    }
}
