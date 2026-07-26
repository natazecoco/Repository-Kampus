<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Publication;
use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_show_page_displays_recommendations(): void
    {
        $container = Container::create(['name' => 'Perpustakaan', 'type' => 'university', 'identifier' => 'perpus']);

        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Publikasi Utama',
            'author' => 'Dewi',
            'year' => 2026,
            'abstract' => 'Abstrak utama tentang basis data dan sistem informasi.',
            'keywords' => 'basis data, sistem informasi',
        ]);

        $recommended = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Publikasi Serupa',
            'author' => 'Eko',
            'year' => 2025,
            'abstract' => 'Abstrak tentang sistem informasi dan pengolahan data.',
            'keywords' => 'sistem informasi, data',
        ]);

        Recommendation::create([
            'publication_id' => $publication->id,
            'recommended_id' => $recommended->id,
            'similarity_score' => 0.87,
        ]);

        $response = $this->get(route('publications.show', $publication));

        $response->assertStatus(200);
        $response->assertSeeText('Rekomendasi Bacaan');
        $response->assertSeeText('Publikasi Serupa');
        $response->assertSeeText('Eko');
        $response->assertSeeText('87%');
    }
}
