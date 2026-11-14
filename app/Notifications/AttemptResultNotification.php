<?php

namespace App\Notifications;

use App\Models\Attempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttemptResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Attempt $attempt) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->attempt->loadMissing('tryout');

        return (new MailMessage)
            ->subject('Hasil '.$this->attempt->tryout->title.' · Latihan CPNS')
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Tryout '.$this->attempt->tryout->title.' sudah dinilai.')
            ->line('Skor total '.$this->attempt->total_score.' — TWK '.$this->attempt->score_twk.', TIU '.$this->attempt->score_tiu.', TKP '.$this->attempt->score_tkp.'.')
            ->line($this->attempt->is_passed ? 'Status: memenuhi passing grade.' : 'Status: belum memenuhi passing grade.')
            ->action('Lihat hasil & pembahasan', route('results.show', $this->attempt))
            ->line('Terus latihan untuk meningkatkan skor.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'attempt_id' => $this->attempt->id,
            'tryout_id' => $this->attempt->tryout_id,
            'total_score' => $this->attempt->total_score,
        ];
    }
}
