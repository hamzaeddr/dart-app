<?php

namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptStatusChanged extends Notification implements ShouldQueue
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
        $status = $this->contribution->status;
        $subject = $status === 'confirmed' ? 'Receipt confirmed' : 'Receipt status updated';

        $message = (new MailMessage)
            ->subject($subject)
            ->line("Your contribution for daret '{$daret->name}' (cycle #{$cycle->cycle_number}) is now '{$status}'.");

        if ($status === 'rejected' && $this->contribution->rejection_reason) {
            $message->line('Reason: '.$this->contribution->rejection_reason);
        }

        return $message->action('View daret', route('darets.show', $daret));
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
            'amount' => $this->contribution->amount,
            'status' => $this->contribution->status,
            'rejection_reason' => $this->contribution->rejection_reason,
            'is_admin_override' => $this->contribution->is_admin_override,
            'type' => 'status_changed',
        ];
    }
}
