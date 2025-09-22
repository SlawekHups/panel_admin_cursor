<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InPostWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        // Validate webhook signature
        if (!$this->validateSignature($request)) {
            Log::warning('Invalid InPost webhook signature', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return response('Unauthorized', 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'required|string',
            'status' => 'required|string',
            'timestamp' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Invalid InPost webhook data', [
                'errors' => $validator->errors(),
                'data' => $request->all(),
            ]);
            return response('Bad Request', 400);
        }

        $trackingNumber = $request->input('tracking_number');
        $status = $request->input('status');
        $timestamp = $request->input('timestamp');

        // Find shipment by tracking number
        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

        if (!$shipment) {
            Log::warning('Shipment not found for tracking number', [
                'tracking_number' => $trackingNumber,
            ]);
            return response('Shipment not found', 404);
        }

        // Update shipment status
        $shipment->update([
            'status' => $this->mapInPostStatus($status),
            'payload' => array_merge($shipment->payload ?? [], [
                'last_status_update' => [
                    'status' => $status,
                    'timestamp' => $timestamp,
                ],
            ]),
        ]);

        // Log status update
        Log::info('InPost shipment status updated', [
            'tracking_number' => $trackingNumber,
            'status' => $status,
            'shipment_id' => $shipment->id,
        ]);

        return response('OK', 200);
    }

    private function validateSignature(Request $request): bool
    {
        // Implement webhook signature validation
        // This is a placeholder - implement based on your InPost webhook configuration
        $signature = $request->header('X-InPost-Signature');
        
        // For now, just check if signature exists
        return !empty($signature);
    }

    private function mapInPostStatus(string $inPostStatus): string
    {
        $statusMap = [
            'created' => 'created',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'returned' => 'returned',
            'lost' => 'lost',
        ];

        return $statusMap[$inPostStatus] ?? 'created';
    }
}
