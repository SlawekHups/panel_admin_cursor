<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shipment $shipment,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Shipment #{$this->shipment->tracking_number} Status Updated")
            ->greeting('Hello!')
            ->line("Your shipment status has been updated.")
            ->line("Tracking Number: {$this->shipment->tracking_number}")
            ->line("Status changed from {$this->oldStatus} to {$this->newStatus}");

        if ($this->shipment->label_path) {
            $message->action('Download Label', url('storage/' . $this->shipment->label_path));
        }

        return $message->line('Thank you for your business!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'order_number' => $this->shipment->order->number,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
