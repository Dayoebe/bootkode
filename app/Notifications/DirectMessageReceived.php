<?php

namespace App\Notifications;

use App\Models\Messaging\DirectMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DirectMessageReceived extends Notification
{
    use Queueable;

    public function __construct(public DirectMessage $directMessage)
    {
        $this->directMessage->loadMissing([
            'sender',
            'conversation.course',
        ]);
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->shouldReceiveEmailNotification('direct_message')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $conversation = $this->directMessage->conversation;

        return (new MailMessage)
            ->subject('New message from ' . $this->directMessage->sender->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->directMessage->sender->name . ' sent you a message about ' . $conversation->course->title . '.')
            ->line(Str::limit($this->directMessage->body, 160))
            ->action('Open Conversation', route('messages.show', ['conversation' => $conversation->id]));
    }

    public function toArray($notifiable): array
    {
        $conversation = $this->directMessage->conversation;

        return [
            'type' => 'direct_message',
            'message' => $this->directMessage->sender->name . ' sent you a message about "' . $conversation->course->title . '".',
            'conversation_id' => $conversation->id,
            'message_id' => $this->directMessage->id,
            'course_id' => $conversation->course_id,
            'course_title' => $conversation->course->title,
            'sender_id' => $this->directMessage->sender_id,
            'sender_name' => $this->directMessage->sender->name,
            'action_url' => route('messages.show', ['conversation' => $conversation->id]),
            'icon' => 'fas fa-comments',
        ];
    }
}
