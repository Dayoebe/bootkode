<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TicketUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        // Add 'mail' if user has a preference for support notifications (add to Settings if needed)
        if ($notifiable->shouldReceiveEmailNotification('support_ticket')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Update on Your Support Ticket: ' . $this->ticket->subject)
            ->line('Your support ticket has been updated to ' . ucfirst($this->ticket->status) . '.')
            ->line('Response: ' . ($this->ticket->response ?? 'No response yet.'))
            ->action('View Ticket', route('help.support')) // Link to ticket history
            ->line('Thank you for your patience!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'support_ticket_update',
            'message' => 'Your support ticket "' . $this->ticket->subject . '" has been updated to ' . ucfirst($this->ticket->status) . '.',
            'action_url' => route('help.support'),
            'icon' => 'fas fa-ticket-alt',
        ]);
    }
}