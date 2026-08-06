<?php

namespace Tests\Unit;

use App\Models\Publication;
use Tests\TestCase;

class PublicationMetadataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', ['--path' => 'database/migrations/2026_07_14_074123_create_containers_table.php']);
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_07_14_074225_create_publications_table.php']);
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_07_21_140345_create_publication_files_table.php']);
    }

    public function test_metadata_summary_returns_standardized_document_information(): void
    {
        $container = \App\Models\Container::create([
            'name' => 'Container Uji',
            'type' => 'university',
        ]);

        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Pengujian Metadata',
            'author' => 'Author Test',
            'year' => 2026,
            'abstract' => 'Abstrak untuk pengujian metadata.',
            'keywords' => 'metadata, thesis',
        ]);

        $metadata = $publication->metadata_summary;

        $this->assertSame('Skripsi / Tesis / Disertasi', $metadata['document_type']);
        $this->assertSame('Dokumen Akademik', $metadata['category']);
        $this->assertSame('thesis', $metadata['type']);
    }

    public function test_admin_completion_state_reports_missing_required_sections(): void
    {
        $container = \App\Models\Container::create([
            'name' => 'Container Uji',
            'type' => 'university',
        ]);

        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Kelengkapan Admin',
            'author' => 'Admin Test',
            'year' => 2026,
            'abstract' => 'Abstrak uji kelengkapan admin.',
            'keywords' => 'admin, completeness',
        ]);

        $completion = $publication->admin_completion_state;

        $this->assertFalse($completion['is_complete']);
        $this->assertSame('Perlu Dilengkapi', $completion['status_label']);
        $this->assertContains('cover', $completion['missing_sections']);
    }

    public function test_type_filter_options_include_document_categories_for_homepage_filters(): void
    {
        $options = Publication::typeFilterOptions();

        $this->assertArrayHasKey('', $options);
        $this->assertSame('Semua Kategori', $options['']);
        $this->assertSame('Skripsi / Tesis / Disertasi', $options['thesis']);
        $this->assertSame('Artikel Jurnal', $options['article']);
    }
}
