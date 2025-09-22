<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\PullPrestaShopOrdersJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PrestaShopWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        // Validate webhook signature (implement based on your PrestaShop setup)
        if (!$this->validateSignature($request)) {
            Log::warning('Invalid PrestaShop webhook signature', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return response('Unauthorized', 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'event' => 'required|string',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            Log::warning('Invalid PrestaShop webhook data', [
                'errors' => $validator->errors(),
                'data' => $request->all(),
            ]);
            return response('Bad Request', 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        // Process webhook based on event type
        switch ($event) {
            case 'order.created':
            case 'order.updated':
                $this->handleOrderEvent($data);
                break;
            case 'customer.created':
            case 'customer.updated':
                $this->handleCustomerEvent($data);
                break;
            case 'product.created':
            case 'product.updated':
                $this->handleProductEvent($data);
                break;
            default:
                Log::info('Unhandled PrestaShop webhook event', [
                    'event' => $event,
                    'data' => $data,
                ]);
        }

        return response('OK', 200);
    }

    private function handleOrderEvent(array $data): void
    {
        if (isset($data['id'])) {
            // Dispatch job to sync specific order
            PullPrestaShopOrdersJob::dispatch(null, 1, 1);
        }
    }

    private function handleCustomerEvent(array $data): void
    {
        // Dispatch job to sync customers
        // SyncCustomersJob::dispatch();
    }

    private function handleProductEvent(array $data): void
    {
        // Dispatch job to sync products
        // SyncProductsJob::dispatch();
    }

    private function validateSignature(Request $request): bool
    {
        // Implement webhook signature validation
        // This is a placeholder - implement based on your PrestaShop webhook configuration
        $signature = $request->header('X-PrestaShop-Signature');
        $payload = $request->getContent();
        
        // For now, just check if signature exists
        return !empty($signature);
    }
}
