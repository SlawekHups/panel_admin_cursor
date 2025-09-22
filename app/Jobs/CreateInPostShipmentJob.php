<?php

namespace App\Jobs;

use App\Integrations\InPost\InPostClient;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateInPostShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(InPostClient $inPostClient): void
    {
        try {
            $order = Order::with(['customer', 'orderItems'])->findOrFail($this->orderId);

            // Check if shipment already exists
            if ($order->shipment) {
                Log::info('Shipment already exists for order', ['order_id' => $this->orderId]);
                return;
            }

            // Prepare shipment payload
            $payload = $this->prepareShipmentPayload($order);

            // Create shipment via InPost API
            $response = $inPostClient->createShipment($payload);

            // Create shipment record
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier' => 'inpost',
                'service_type' => 'parcel_locker',
                'tracking_number' => $response['tracking_number'] ?? null,
                'status' => 'created',
                'payload' => $response,
            ]);

            // Download and store label
            if (isset($response['tracking_number'])) {
                $this->downloadAndStoreLabel($inPostClient, $response['tracking_number'], $shipment);
            }

            Log::info('InPost shipment created successfully', [
                'order_id' => $this->orderId,
                'tracking_number' => $shipment->tracking_number,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create InPost shipment', [
                'error' => $e->getMessage(),
                'order_id' => $this->orderId,
            ]);
            throw $e;
        }
    }

    private function prepareShipmentPayload(Order $order): array
    {
        return [
            'receiver' => [
                'name' => $order->customer->full_name,
                'email' => $order->customer->email,
                'phone' => $order->customer->phone,
            ],
            'parcels' => [
                [
                    'id' => $order->number,
                    'dimensions' => [
                        'length' => 20,
                        'width' => 20,
                        'height' => 20,
                    ],
                    'weight' => 1.0,
                ],
            ],
            'service' => 'parcel_locker',
            'reference' => $order->number,
        ];
    }

    private function downloadAndStoreLabel(InPostClient $inPostClient, string $trackingNumber, Shipment $shipment): void
    {
        try {
            $labelContent = $inPostClient->getLabel($trackingNumber);
            
            $filename = "labels/{$trackingNumber}.pdf";
            Storage::disk('public')->put($filename, $labelContent);
            
            $shipment->update([
                'label_path' => $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to download InPost label', [
                'error' => $e->getMessage(),
                'tracking_number' => $trackingNumber,
            ]);
        }
    }
}
