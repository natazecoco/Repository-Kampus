<?php

namespace Tests\Unit;

use App\Models\Topic;
use App\Models\User;
use App\Policies\TopicPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_merge_and_undo_merge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $topic = Topic::create(['name' => 'Topik A', 'slug' => 'topik-a', 'is_active' => true, 'sort_order' => 1]);

        $policy = new TopicPolicy();

        $this->assertTrue($policy->merge($admin, $topic));
        $this->assertTrue($policy->undoMerge($admin, $topic));
    }

    public function test_non_admin_user_cannot_merge_or_undo_merge(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $topic = Topic::create(['name' => 'Topik B', 'slug' => 'topik-b', 'is_active' => true, 'sort_order' => 2]);

        $policy = new TopicPolicy();

        $this->assertFalse($policy->merge($user, $topic));
        $this->assertFalse($policy->undoMerge($user, $topic));
    }
}
