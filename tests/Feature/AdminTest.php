<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    public function test_peserta_cannot_reach_admin_area(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_create_question_with_options(): void
    {
        $subtest = Subtest::create(['code' => 'TIU', 'name' => 'TIU', 'question_count' => 35, 'passing_grade' => 80]);

        $options = collect(['A', 'B', 'C', 'D', 'E'])->map(fn (string $label): array => [
            'label' => $label,
            'body' => "Isi {$label}",
            'is_correct' => $label === 'B',
            'weight' => $label === 'B' ? 5 : 0,
        ])->all();

        $this->actingAs($this->admin())->post('/admin/questions', [
            'subtest_id' => $subtest->id,
            'body' => 'Soal baru?',
            'difficulty' => 2,
            'options' => $options,
        ])->assertRedirect('/admin/questions');

        $question = Question::with('options')->sole();
        $this->assertCount(5, $question->options);
        $this->assertSame('B', $question->options->firstWhere('is_correct', true)->label);
    }

    public function test_admin_can_deactivate_other_user_but_not_self(): void
    {
        $admin = $this->admin();
        $peserta = User::factory()->create();

        $this->actingAs($admin)->patch("/admin/users/{$peserta->id}/toggle")->assertRedirect();
        $this->assertFalse($peserta->refresh()->is_active);

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle")->assertForbidden();
    }
}
