<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
