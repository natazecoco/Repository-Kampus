<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_topic_filters(): void
    {
        Topic::create(['name' => 'Kecerdasan Buatan', 'slug' => 'kecerdasan-buatan', 'is_active' => true, 'sort_order' => 1]);
        Topic::create(['name' => 'Rekayasa Perangkat Lunak', 'slug' => 'rekayasa-perangkat-lunak', 'is_active' => true, 'sort_order' => 2]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeText('Kecerdasan Buatan');
        $response->assertSeeText('Rekayasa Perangkat Lunak');
    }

    public function test_topic_page_filters_publications_by_topic(): void
    {
        $topicA = Topic::create(['name' => 'Sistem Informasi', 'slug' => 'sistem-informasi', 'is_active' => true, 'sort_order' => 1]);
        $topicB = Topic::create(['name' => 'Data Mining', 'slug' => 'data-mining', 'is_active' => true, 'sort_order' => 2]);

        $container = Container::create(['name' => 'Perpustakaan', 'type' => 'university', 'identifier' => 'perpus']);

        Publication::create(['title' => 'Publikasi A', 'author' => 'Andi', 'year' => '2025', 'abstract' => 'Contoh abstrak', 'keywords' => 'data', 'container_id' => $container->id]);
        Publication::create(['title' => 'Publikasi B', 'author' => 'Budi', 'year' => '2024', 'abstract' => 'Contoh lain', 'keywords' => 'sistem', 'container_id' => $container->id]);

        $publicationA = Publication::firstWhere('title', 'Publikasi A');
        $publicationB = Publication::firstWhere('title', 'Publikasi B');

        $publicationA->topics()->attach($topicA->id);
        $publicationB->topics()->attach($topicB->id);

        $response = $this->get(route('topic.show', $topicA->slug));

        $response->assertStatus(200);
        $response->assertSeeText('Menampilkan publikasi yang terkait dengan topik');
        $response->assertSeeText('Sistem Informasi');
        $response->assertSeeText('Publikasi A');
        $response->assertDontSeeText('Publikasi B');
    }

    public function test_inactive_topics_do_not_appear_in_topic_list(): void
    {
        Topic::create(['name' => 'Topik Aktif', 'slug' => 'topik-aktif', 'is_active' => true, 'sort_order' => 1]);
        Topic::create(['name' => 'Topik Nonaktif', 'slug' => 'topik-nonaktif', 'is_active' => false, 'sort_order' => 2]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeText('Topik Aktif');
        $response->assertDontSeeText('Topik Nonaktif');
    }
}
