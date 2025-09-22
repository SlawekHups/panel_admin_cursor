<?php

namespace App\Jobs;

use App\Integrations\PrestaShop\PrestaShopClient;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PullPrestaShopOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Carbon $since;
    private int $page;
    private int $perPage;

    public function __construct(?Carbon $since = null, int $page = 1, int $perPage = 100)
    {
        $this->since = $since;
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function handle(PrestaShopClient $prestaShopClient): void
    {
        try {
            $orders = $prestaShopClient->getOrders($this->since, $this->page, $this->perPage);

            foreach ($orders as $orderData) {
                $this->processOrder($orderData);
            }

            // If we got a full page, schedule next page
            if (count($orders) === $this->perPage) {
                self::dispatch($this->since, $this->page + 1, $this->perPage)
                    ->delay(now()->addSeconds(5));
            }
        } catch (\Exception $e) {
            Log::error('Failed to pull PrestaShop orders', [
                'error' => $e->getMessage(),
                'since' => $this->since,
                'page' => $this->page,
            ]);
            throw $e;
        }
    }

    private function processOrder(array $orderData): void
    {
        // Create or update customer
        $customer = Customer::updateOrCreate(
            ['external_id' => $orderData['id_customer']],
            [
                'email' => $orderData['customer']['email'],
                'first_name' => $orderData['customer']['firstname'],
                'last_name' => $orderData['customer']['lastname'],
                'payload' => $orderData['customer'],
            ]
        );

        // Create or update order
        $order = Order::updateOrCreate(
            ['external_id' => $orderData['id']],
            [
                'number' => $orderData['reference'],
                'customer_id' => $customer->id,
                'status' => $this->mapOrderStatus($orderData['current_state']),
                'currency' => $orderData['currency']['iso_code'],
                'total_gross' => $orderData['total_paid_tax_incl'],
                'total_net' => $orderData['total_paid_tax_excl'],
                'shipping_method' => $orderData['carrier']['name'] ?? null,
                'paid_at' => $orderData['date_add'] ? Carbon::parse($orderData['date_add']) : null,
                'payload' => $orderData,
            ]
        );

        // Process order items
        if (isset($orderData['associations']['order_rows'])) {
            foreach ($orderData['associations']['order_rows'] as $itemData) {
                $this->processOrderItem($order, $itemData);
            }
        }
    }

    private function processOrderItem(Order $order, array $itemData): void
    {
        // Try to find product by external ID
        $product = Product::where('external_id', $itemData['product_id'])->first();

        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'sku' => $itemData['product_reference'],
            ],
            [
                'product_id' => $product?->id,
                'name' => $itemData['product_name'],
                'qty' => $itemData['product_quantity'],
                'price_gross' => $itemData['total_price_tax_incl'],
                'price_net' => $itemData['total_price_tax_excl'],
                'tax_rate' => $itemData['tax_rate'] ?? 0,
                'payload' => $itemData,
            ]
        );
    }

    private function mapOrderStatus(string $prestaShopStatus): string
    {
        $statusMap = [
            '1' => 'pending',      // Awaiting check payment
            '2' => 'processing',   // Payment accepted
            '3' => 'processing',   // Preparing in progress
            '4' => 'shipped',      // Shipped
            '5' => 'delivered',    // Delivered
            '6' => 'cancelled',    // Canceled
            '7' => 'refunded',     // Refunded
        ];

        return $statusMap[$prestaShopStatus] ?? 'pending';
    }
}
