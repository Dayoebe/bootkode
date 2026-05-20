<?php

namespace App\Notifications;

use App\Models\Admin\InstitutionInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstitutionAdminInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected InstitutionInvitation $invitation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation to manage ' . $this->invitation->institution->name)
            ->greeting('Hello ' . ($this->invitation->name ?: $notifiable->name) . ',')
            ->line('You have been invited as ' . $this->invitation->role_name . ' for ' . $this->invitation->institution->name . '.')
            ->line('This invitation gives you access to the institution portal, learner management, cohorts, and reports for your school.')
            ->action('Accept Invitation', route('institution.invitations.accept', $this->invitation->token))
            ->line('This invitation expires on ' . $this->invitation->expires_at->format('M d, Y') . '.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Institution Admin Invitation',
            'message' => 'You were invited to manage ' . $this->invitation->institution->name . '.',
            'institution_id' => $this->invitation->institution_id,
            'invitation_id' => $this->invitation->id,
            'type' => 'institution_admin_invitation',
            'action_url' => route('institution.invitations.accept', $this->invitation->token),
        ];
    }
}
