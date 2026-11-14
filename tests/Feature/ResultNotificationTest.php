<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Models\User;
use App\Notifications\AttemptResultNotification;
use App\Services\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResultNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_email_is_sent_once_after_attempt_is_scored(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['title' => 'Tryout Perdana']);
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        app(ExamService::class)->submit($attempt);
        app(ExamService::class)->submit($attempt->fresh());

        Notification::assertSentToTimes($user, AttemptResultNotification::class, 1);
        Notification::assertSentTo(
            $user,
            AttemptResultNotification::class,
            function (AttemptResultNotification $notification) use ($user): bool {
                $mail = $notification->toMail($user);

                return $notification->attempt->tryout->title === 'Tryout Perdana'
                    && str_contains($mail->subject, 'Tryout Perdana');
            },
        );
    }
}
