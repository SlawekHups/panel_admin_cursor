<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invitation $invitation
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('invite.accept', $this->invitation->token);

        return (new MailMessage)
            ->subject('Invitation to Admin Panel')
            ->greeting('Hello!')
            ->line('You have been invited to join our admin panel.')
            ->line('Click the button below to accept the invitation and create your account.')
            ->action('Accept Invitation', $url)
            ->line('This invitation will expire on ' . $this->invitation->expires_at->format('Y-m-d H:i:s'))
            ->line('If you did not expect this invitation, please ignore this email.');
    }

    public function toArray($notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'token' => $this->invitation->token,
            'expires_at' => $this->invitation->expires_at,
        ];
    }
}
