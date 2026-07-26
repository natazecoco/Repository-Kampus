<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Publication;
use App\Models\PublicationFile;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_page_can_filter_publications_by_topic(): void
    {
        $topic = Topic::create([
            'name' => 'Kecerdasan Buatan',
            'slug' => 'kecerdasan-buatan',
        ]);

        $otherTopic = Topic::create([
            'name' => 'Sistem Informasi',
            'slug' => 'sistem-informasi',
        ]);

        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $matchingPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Publikasi AI',
            'author' => 'Penulis AI',
            'year' => 2026,
            'abstract' => 'Abstrak untuk pengujian filter topik.',
            'keywords' => 'topik, ai',
        ]);
        $matchingPublication->topics()->attach($topic->id);

        $otherPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Publikasi SI',
            'author' => 'Penulis SI',
            'year' => 2026,
            'abstract' => 'Abstrak untuk pengujian filter topik lainnya.',
            'keywords' => 'topik, si',
        ]);
        $otherPublication->topics()->attach($otherTopic->id);

        $response = $this->get(route('home', ['topic' => $topic->slug]));

        $response->assertOk();
        $response->assertSee($matchingPublication->title);
        $response->assertDontSee($otherPublication->title);
        $response->assertSee($topic->name);
    }

    public function test_a_registered_user_is_a_student_and_can_log_in_with_npm(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.com',
            'npm' => '12345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'npm' => '12345678',
            'role' => 'student',
        ]);

        $this->post('/login', [
            'npm' => '12345678',
            'password' => 'password123',
        ])->assertRedirect('/');
    }

    public function test_only_administrators_can_access_the_filament_panel(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($student->canAccessPanel(app('filament')->getPanel('admin')));
        $this->assertTrue($admin->canAccessPanel(app('filament')->getPanel('admin')));
    }

    public function test_document_access_respects_visibility_and_download_permissions(): void
    {
        $publication = $this->makePublication();
        $publicFile = PublicationFile::create([
            'publication_id' => $publication->id,
            'section' => 'cover',
            'title' => 'Cover',
            'file_path' => 'publications_pdf/cover.pdf',
            'access_type' => 'public',
            'visibility' => 'public',
            'allow_download' => false,
        ]);
        $internalFile = PublicationFile::create([
            'publication_id' => $publication->id,
            'section' => 'chapter_1',
            'title' => 'Bab I',
            'file_path' => 'publications_pdf/chapter-1.pdf',
            'access_type' => 'restricted',
            'visibility' => 'authenticated',
            'allow_download' => false,
        ]);
        $adminFile = PublicationFile::create([
            'publication_id' => $publication->id,
            'section' => 'approval_sheet',
            'title' => 'Lembar Pengesahan',
            'file_path' => 'publications_pdf/approval.pdf',
            'access_type' => 'restricted',
            'visibility' => 'admin',
            'allow_download' => false,
        ]);

        $this->get(route('publications.viewer', $publication))->assertOk()->assertSee('Cover')->assertDontSee('Bab I');
        $this->get(route('file.akses', $publicFile))->assertForbidden();
        $this->get(route('publications.viewer', ['publication' => $publication, 'file' => $internalFile]))->assertOk();

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)
            ->get(route('publications.viewer', ['publication' => $publication, 'file' => $internalFile]))
            ->assertOk()
            ->assertSee('Bab I')
            ->assertDontSee('Lembar Pengesahan');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('publications.viewer', ['publication' => $publication, 'file' => $adminFile]))
            ->assertOk()
            ->assertSee('Lembar Pengesahan');
    }

    public function test_a_download_is_only_available_when_explicitly_allowed(): void
    {
        Storage::fake('local');
        $publication = $this->makePublication();
        $file = PublicationFile::create([
            'publication_id' => $publication->id,
            'section' => 'full_document',
            'title' => 'Artikel Terbuka',
            'file_path' => 'publications_pdf/article.pdf',
            'access_type' => 'public',
            'visibility' => 'public',
            'allow_download' => true,
        ]);
        Storage::disk('local')->put($file->file_path, '%PDF-1.4 test');

        $this->get(route('file.akses', $file))->assertOk();
    }

    private function makePublication(): Publication
    {
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        return Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Dokumen Test',
            'author' => 'Penulis Test',
            'year' => 2026,
            'abstract' => 'Abstrak untuk pengujian akses berkas.',
            'keywords' => 'akses, dokumen',
        ]);
    }
}
