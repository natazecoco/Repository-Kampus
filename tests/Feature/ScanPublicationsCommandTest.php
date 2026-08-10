<?php

namespace Tests\Feature;

use App\Jobs\GenerateRecommendations;
use App\Models\Container;
use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScanPublicationsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_research_method_and_dispatches_recommendation_job(): void
    {
        Queue::fake();

        Container::create([
            'name' => 'Universitas Gunadarma',
            'type' => 'university',
        ]);

        $publication = Publication::create([
            'container_id' => 1,
            'title' => 'Sistem Informasi Akademik',
            'author' => 'Budi Santoso',
            'year' => 2024,
            'abstract' => 'Penelitian ini menggunakan metode waterfall untuk membangun sistem informasi.',
            'keywords' => 'waterfall, sistem, pengembangan',
            'type' => 'thesis',
            'research_method' => null,
        ]);

        $this->artisan('repo:scan-ulang')->assertExitCode(0);

        $publication->refresh();

        $this->assertSame('SDLC Waterfall', $publication->research_method);
        Queue::assertPushed(GenerateRecommendations::class);
    }

    public function test_it_prioritizes_a_development_method_in_the_title_over_a_technology_term(): void
    {
        Queue::fake();

        Container::create([
            'name' => 'Universitas Gunadarma',
            'type' => 'university',
        ]);

        $publication = Publication::create([
            'container_id' => 1,
            'title' => 'Pengembangan Sistem Informasi Menggunakan RAD',
            'author' => 'Budi Santoso',
            'year' => 2024,
            'abstract' => 'Sistem menggunakan machine learning sebagai teknologi pendukung.',
            'keywords' => 'RAD, machine learning, sistem informasi',
            'type' => 'thesis',
            'research_method' => null,
        ]);

        $this->artisan('repo:scan-ulang')->assertExitCode(0);

        $this->assertSame('Rapid Application Development (RAD)', $publication->fresh()->research_method);
    }

    public function test_short_aliases_do_not_match_inside_unrelated_words(): void
    {
        Queue::fake();

        Container::create([
            'name' => 'Universitas Gunadarma',
            'type' => 'university',
        ]);

        $publication = Publication::create([
            'container_id' => 1,
            'title' => 'Analisis Kepuasan Pengguna Aplikasi',
            'author' => 'Budi Santoso',
            'year' => 2024,
            'abstract' => 'Penelitian menggunakan kuesioner dan analisis deskriptif.',
            'keywords' => 'kepuasan pengguna, analisis, kuesioner',
            'type' => 'thesis',
            'research_method' => null,
        ]);

        $this->artisan('repo:scan-ulang')->assertExitCode(0);

        $this->assertSame('Deskriptif', $publication->fresh()->research_method);
    }
}
