<?php
// app/Notifications/MarketplaceItemApproved.php
namespace App\Notifications;

use App\Models\MarketplaceItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketplaceItemApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $item;

    public function __construct(MarketplaceItem $item)
    {
        $this->item = $item;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your marketplace item has been approved!')
            ->greeting('Great news!')
            ->line("Your item '{$this->item->title}' has been approved and is now live on the marketplace.")
            ->action('View Item', route('marketplace.item.public', $this->item->slug))
            ->line('Customers can now discover and purchase your item.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'marketplace_item_approved',
            'item_id' => $this->item->id,
            'item_title' => $this->item->title,
            'message' => "Your item '{$this->item->title}' has been approved!",
        ];
    }
}
