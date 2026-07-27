<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Publication;
use App\Models\PublicationFile;
use App\Models\Recommendation;
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

        // Also ensure the dedicated topic page route works
        $response2 = $this->get(route('topic.show', $topic->slug));
        $response2->assertOk();
        $response2->assertSee($matchingPublication->title);
        $response2->assertDontSee($otherPublication->title);
        $response2->assertSee($topic->name);
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

        $response->assertRedirect();
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

    public function test_authenticated_user_can_view_their_bookmarks_page(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);
        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Dokumen Tersimpan',
            'author' => 'Penulis Tersimpan',
            'year' => 2026,
            'abstract' => 'Abstrak untuk bookmark.',
            'keywords' => 'bookmark, test',
        ]);
        $publication->topics()->attach(Topic::create([
            'name' => 'Rekomendasi',
            'slug' => 'rekomendasi',
        ])->id);

        $user->bookmarks()->create(['publication_id' => $publication->id]);

        $response = $this->actingAs($user)->get(route('bookmarks.index'));

        $response->assertOk();
        $response->assertSee('Daftar Bacaan Saya');
        $response->assertSee($publication->title);
    }

    public function test_home_page_shows_bookmark_action_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);
        Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Dokumen dengan Tombol Bookmark',
            'author' => 'Penulis Bookmark',
            'year' => 2026,
            'abstract' => 'Abstrak untuk tombol bookmark.',
            'keywords' => 'bookmark, home',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('☆ Simpan');
    }

    public function test_home_page_shows_personalized_recommendations_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $topic = Topic::create([
            'name' => 'Machine Learning',
            'slug' => 'machine-learning',
        ]);

        $bookmarkedPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Skripsi tentang AI',
            'author' => 'Penulis AI',
            'year' => 2026,
            'abstract' => 'Abstrak terkait AI.',
            'keywords' => 'ai, machine learning',
        ]);
        $bookmarkedPublication->topics()->attach($topic->id);

        $recommendedPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'article',
            'title' => 'Artikel yang direkomendasikan',
            'author' => 'Penulis Rekomendasi',
            'year' => 2026,
            'abstract' => 'Abstrak yang relevan dengan machine learning.',
            'keywords' => 'recommendation, machine learning',
        ]);
        $recommendedPublication->topics()->attach($topic->id);

        $user->bookmarks()->create(['publication_id' => $bookmarkedPublication->id]);
        $user->topicPreferences()->create(['topic_id' => $topic->id, 'preference_type' => 'interest']);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('Rekomendasi untukmu');
        $response->assertSee($recommendedPublication->title);
    }

    public function test_recommendations_can_use_topic_hierarchy_for_knowledge_based_scoring(): void
    {
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $parentTopic = Topic::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'is_active' => true,
        ]);
        $childTopic = Topic::create([
            'name' => 'MVC',
            'slug' => 'mvc',
            'parent_id' => $parentTopic->id,
            'is_active' => true,
        ]);

        $targetPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Laravel dari Nol',
            'author' => 'Penulis Laravel',
            'year' => 2026,
            'abstract' => 'Panduan dasar untuk mengembangkan aplikasi web.',
            'keywords' => 'laravel, framework, backend',
        ]);
        $targetPublication->topics()->attach($parentTopic->id);

        $relatedPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'article',
            'title' => 'Teknik Arsitektur Aplikasi Modern',
            'author' => 'Penulis Lain',
            'year' => 2026,
            'abstract' => 'Studi tentang pola desain aplikasi yang terstruktur.',
            'keywords' => 'architecture, patterns, modularity',
        ]);
        $relatedPublication->topics()->attach($childTopic->id);

        $job = new \App\Jobs\GenerateRecommendations($targetPublication);
        $job->handle();

        $this->assertTrue(
            Recommendation::where('publication_id', $targetPublication->id)
                ->where('recommended_id', $relatedPublication->id)
                ->exists()
        );
    }

    public function test_search_can_expand_queries_using_topic_hierarchy(): void
    {
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $parentTopic = Topic::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'is_active' => true,
        ]);
        Topic::create([
            'name' => 'MVC',
            'slug' => 'mvc',
            'parent_id' => $parentTopic->id,
            'is_active' => true,
        ]);

        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Pemodelan Arsitektur MVC',
            'author' => 'Penulis MVC',
            'year' => 2026,
            'abstract' => 'Dokumen tentang implementasi arsitektur aplikasi.',
            'keywords' => 'mvc, architecture',
        ]);
        $publication->topics()->attach($parentTopic->id);

        $response = $this->get(route('home', ['search' => 'development']));

        $response->assertOk();
        $response->assertSee($publication->title);
    }

    public function test_authenticated_student_can_open_their_dashboard_and_update_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'name' => 'Mahasiswa Lama',
            'email' => 'lama@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Profil Saya');

        $updateResponse = $this->actingAs($user)->post(route('student.profile.update'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'baru@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $updateResponse->assertRedirect(route('student.dashboard'));
        $this->assertSame('Mahasiswa Baru', $user->fresh()->name);
        $this->assertSame('baru@example.com', $user->fresh()->email);
    }

    public function test_home_page_can_show_topic_hierarchy_and_semantic_related_results(): void
    {
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $parentTopic = Topic::create([
            'name' => 'Artificial Intelligence',
            'slug' => 'artificial-intelligence',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $childTopic = Topic::create([
            'name' => 'Machine Learning',
            'slug' => 'machine-learning',
            'parent_id' => $parentTopic->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $publication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Deep Learning for Recommendation',
            'author' => 'Penulis Test',
            'year' => 2026,
            'abstract' => 'A study on recommendation systems using deep learning.',
            'keywords' => 'recommendation, deep learning',
        ]);
        $publication->topics()->attach($childTopic->id);

        $response = $this->get(route('home', ['search' => 'recommendation systems']));

        $response->assertOk();
        $response->assertSee('Artificial Intelligence');
        $response->assertSee('Machine Learning');
        $response->assertSee($publication->title);
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

    public function test_publication_detail_page_shows_related_recommendations_when_none_are_precomputed(): void
    {
        $container = Container::create([
            'name' => 'Repository Test',
            'type' => 'university',
        ]);

        $targetPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'thesis',
            'title' => 'Deep Learning for Recommendation Systems',
            'author' => 'Penulis Target',
            'year' => 2026,
            'abstract' => 'A study on recommendation systems using deep learning.',
            'keywords' => 'recommendation, deep learning',
        ]);

        $relatedPublication = Publication::create([
            'container_id' => $container->id,
            'type' => 'article',
            'title' => 'Related Recommendation Article',
            'author' => 'Penulis Terkait',
            'year' => 2026,
            'abstract' => 'An article about recommendation systems and deep learning.',
            'keywords' => 'recommendation, deep learning',
        ]);

        $response = $this->get(route('publications.show', $targetPublication));

        $response->assertOk();
        $response->assertSee('Rekomendasi Serupa');
        $response->assertSee($relatedPublication->title);
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
