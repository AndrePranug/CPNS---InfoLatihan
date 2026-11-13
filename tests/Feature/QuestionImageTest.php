<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionImageTest extends TestCase
{
    use RefreshDatabase;

    private function question(): Question
    {
        $subtest = Subtest::query()->firstOrCreate(
            ['code' => 'TWK'],
            ['name' => 'Tes Wawasan Kebangsaan', 'question_count' => 30, 'passing_grade' => 65],
        );
        $topic = Topic::query()->firstOrCreate(['subtest_id' => $subtest->id, 'name' => 'Pancasila']);

        return Question::factory()->create(['subtest_id' => $subtest->id, 'topic_id' => $topic->id]);
    }

    public function test_admin_can_upload_question_image(): void
    {
        Storage::fake('public');
        $question = $this->question();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post("/admin/questions/{$question->id}/image", [
                'image' => UploadedFile::fake()->image('soal.png', 600, 400),
            ])
            ->assertSessionHas('success');

        $question->refresh();

        $this->assertNotNull($question->image_path);
        $this->assertStringStartsWith('/storage/questions/', $question->image_path);
        Storage::disk('public')->assertExists(str($question->image_path)->after('/storage/')->value());
    }

    public function test_uploading_replaces_previous_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('questions/lama.png', 'lama');
        $question = $this->question();
        $question->update(['image_path' => '/storage/questions/lama.png']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post("/admin/questions/{$question->id}/image", [
                'image' => UploadedFile::fake()->image('baru.jpg'),
            ])
            ->assertSessionHas('success');

        Storage::disk('public')->assertMissing('questions/lama.png');
        Storage::disk('public')->assertExists(str($question->fresh()->image_path)->after('/storage/')->value());
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $question = $this->question();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post("/admin/questions/{$question->id}/image", [
                'image' => UploadedFile::fake()->create('soal.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertNull($question->fresh()->image_path);
    }

    public function test_participant_cannot_upload_question_image(): void
    {
        Storage::fake('public');
        $question = $this->question();

        $this->actingAs(User::factory()->create())
            ->post("/admin/questions/{$question->id}/image", [
                'image' => UploadedFile::fake()->image('soal.png'),
            ])
            ->assertForbidden();

        $this->assertNull($question->fresh()->image_path);
    }
}
