<?php

namespace Tests\Unit;

use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicUndoMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_undo_merge_restores_topic_active_and_restores_publication_relations()
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

        $source = Topic::create(['name' => 'Source', 'slug' => 'source-topic']);
        $target = Topic::create(['name' => 'Target', 'slug' => 'target-topic']);

        $p1->topics()->attach($source->id);

        $source->mergeInto($target);

        $this->assertFalse($source->fresh()->is_active);
        $this->assertDatabaseHas('topic_merge_backups', ['source_id' => $source->id, 'target_id' => $target->id, 'undone' => 0]);

        $ok = $source->undoMerge();
        $this->assertTrue($ok);
        $this->assertTrue($source->fresh()->is_active);
        $this->assertDatabaseHas('topic_merge_backups', ['source_id' => $source->id, 'undone' => 1]);
        $this->assertDatabaseHas('publication_topic', ['publication_id' => $p1->id, 'topic_id' => $source->id]);
    }
}
