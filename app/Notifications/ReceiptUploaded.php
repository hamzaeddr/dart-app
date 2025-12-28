<?php

namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptUploaded extends Notification implements ShouldQueue
{
    use Queueable;

    public Contribution $contribution;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contribution $contribution)
    {
        $this->contribution = $contribution;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daret = $this->contribution->daret;
        $cycle = $this->contribution->cycle;
        $user = $this->contribution->user;

        return (new MailMessage)
            ->subject('New receipt uploaded')
            ->line("{$user->name} uploaded a receipt for daret '{$daret->name}'.")
            ->line("Cycle #{$cycle->cycle_number}, amount: {$this->contribution->amount}.")
            ->action('View daret', route('darets.show', $daret));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contribution_id' => $this->contribution->id,
            'daret_id' => $this->contribution->daret_id,
            'daret_cycle_id' => $this->contribution->daret_cycle_id,
            'user_id' => $this->contribution->user_id,
            'amount' => $this->contribution->amount,
            'status' => $this->contribution->status,
            'type' => 'uploaded',
        ];
    }
}
