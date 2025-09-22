<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->number} Status Updated")
            ->greeting('Hello!')
            ->line("Your order #{$this->order->number} status has been updated.")
            ->line("Status changed from {$this->oldStatus} to {$this->newStatus}")
            ->line("Total: {$this->order->total_gross} PLN")
            ->action('View Order', url('/admin/orders/' . $this->order->id))
            ->line('Thank you for your business!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'customer_email' => $this->order->customer->email,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
