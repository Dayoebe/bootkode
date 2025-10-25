<?php

// app/Notifications/MarketplaceItemRejected.php
namespace App\Notifications;

use App\Models\Marketplace\MarketplaceItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketplaceItemRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $item;
    protected $reason;

    public function __construct(MarketplaceItem $item, $reason)
    {
        $this->item = $item;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your marketplace item needs revisions')
            ->greeting('Hello!')
            ->line("Your item '{$this->item->title}' requires some changes before it can be published.")
            ->line("Reason: {$this->reason}")
            ->action('Edit Item', route('marketplace.items.update', $this->item))
            ->line('Please make the necessary changes and resubmit for review.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'marketplace_item_rejected',
            'item_id' => $this->item->id,
            'item_title' => $this->item->title,
            'reason' => $this->reason,
            'message' => "Your item '{$this->item->title}' was rejected.",
        ];
    }
}
