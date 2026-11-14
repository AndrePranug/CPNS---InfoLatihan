<?php

namespace Tests\Feature;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_thread_and_reply(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/forum', [
                'title' => 'Strategi mengerjakan TIU',
                'body' => 'Bagaimana cara membagi waktu untuk soal deret angka?',
            ])
            ->assertRedirect();

        $thread = ForumThread::query()->sole();
        $this->assertSame($user->id, $thread->user_id);

        $this->actingAs($user)
            ->post("/forum/{$thread->id}/replies", ['body' => 'Saya biasa mengerjakan yang mudah dulu.'])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_locked_thread_rejects_new_replies(): void
    {
        $thread = ForumThread::factory()->create(['is_locked' => true]);

        $this->actingAs(User::factory()->create())
            ->post("/forum/{$thread->id}/replies", ['body' => 'Masih bisa masuk?'])
            ->assertSessionHas('error');

        $this->assertSame(0, ForumReply::count());
    }

    public function test_only_owner_or_admin_can_delete_and_admin_can_lock(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ForumThread::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->delete("/forum/{$thread->id}")->assertForbidden();
        $this->assertDatabaseHas('forum_threads', ['id' => $thread->id]);

        $this->actingAs($admin)->patch("/forum/{$thread->id}/lock")->assertRedirect();
        $this->assertTrue($thread->fresh()->is_locked);

        $this->actingAs($owner)->delete("/forum/{$thread->id}")->assertRedirect(route('forum.index'));
        $this->assertDatabaseMissing('forum_threads', ['id' => $thread->id]);
    }
}
