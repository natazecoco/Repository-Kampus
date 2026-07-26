<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TopicBulkToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_topic_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Topic::create(['name' => 'Artificial Intelligence', 'slug' => 'ai', 'is_active' => true, 'sort_order' => 10]);

        $response = $this->actingAs($admin)->get(route('admin.topics.export'));

        $response->assertStatus(200);
        $response->assertHeaderContains('Content-Type', 'text/csv');

        $controller = new \App\Http\Controllers\TopicBulkController();
        $streamedResponse = $controller->export();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $streamedResponse);

        ob_start();
        $streamedResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('Artificial Intelligence', $csv);
        $this->assertStringContainsString('ai', $csv);
    }

    public function test_admin_can_import_topics_from_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $csv = "name,slug,is_active,sort_order\nKecerdasan Buatan,ai,1,5\nSistem Informasi,sistem-informasi,1,10";

        $response = $this->actingAs($admin)->post(route('admin.topics.import.process'), ['csv' => $csv]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', ['slug' => 'ai', 'name' => 'Kecerdasan Buatan']);
        $this->assertDatabaseHas('topics', ['slug' => 'sistem-informasi', 'name' => 'Sistem Informasi']);
    }

    public function test_admin_can_import_topics_from_csv_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $csv = "name,slug,parent,is_active,sort_order\nWeb Pengembangan,web-pengembangan,,1,5\nSistem Operasi,sistem-operasi,web-pengembangan,1,10";
        $file = UploadedFile::fake()->createWithContent('topics.csv', $csv);

        $response = $this->actingAs($admin)->post(route('admin.topics.import.process'), ['csv_file' => $file]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', ['slug' => 'web-pengembangan', 'name' => 'Web Pengembangan']);
        $this->assertDatabaseHas('topics', ['slug' => 'sistem-operasi', 'name' => 'Sistem Operasi']);
    }

    public function test_admin_can_view_duplicate_detection_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Topic::create(['name' => 'Artificial Intelligence', 'slug' => 'ai', 'is_active' => true, 'sort_order' => 1]);
        Topic::create(['name' => 'Artificial Intelligence (AI)', 'slug' => 'artificial-intelligence-ai', 'is_active' => true, 'sort_order' => 2]);

        $response = $this->actingAs($admin)->get(route('admin.topics.duplicates'));

        $response->assertStatus(200);
        $response->assertSee('Artificial Intelligence');
        $response->assertSee('Artificial Intelligence (AI)');
    }
}
