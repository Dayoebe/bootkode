<?php

// app/Notifications/MarketplaceOrderConfirmed.php
namespace App\Notifications;

use App\Models\Marketplace\MarketplaceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketplaceOrderConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(MarketplaceOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Confirmed - #' . $this->order->order_number)
            ->greeting('Thank you for your purchase!')
            ->line("Your order #{$this->order->order_number} has been confirmed.")
            ->line("Item: {$this->order->item->title}")
            ->line("Amount: {$this->order->formatted_total}")
            ->action('View Order', route('marketplace.purchases'))
            ->line('You will receive your access details shortly.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'marketplace_order_confirmed',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'item_title' => $this->order->item->title,
            'amount' => $this->order->total_amount,
            'message' => "Your order #{$this->order->order_number} has been confirmed.",
        ];
    }
}