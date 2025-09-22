<?php

namespace App\Integrations\InPost;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class InPostClient
{
    private Client $http;
    private string $baseUrl;
    private string $apiToken;

    public function __construct(Client $http, string $baseUrl, string $apiToken)
    {
        $this->http = $http;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiToken = $apiToken;
    }

    /**
     * Create shipment
     */
    public function createShipment(array $payload): array
    {
        try {
            $response = $this->request('POST', 'v1/shipments', [
                'json' => $payload,
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('InPost API error creating shipment', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            throw $e;
        }
    }

    /**
     * Get shipment label
     */
    public function getLabel(string $trackingNumber): string
    {
        try {
            $response = $this->request('GET', "v1/shipments/{$trackingNumber}/label", [
                'headers' => [
                    'Accept' => 'application/pdf',
                ],
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('InPost API error getting label', [
                'error' => $e->getMessage(),
                'tracking_number' => $trackingNumber,
            ]);
            throw $e;
        }
    }

    /**
     * Get shipment tracking information
     */
    public function getTracking(string $trackingNumber): array
    {
        try {
            $response = $this->request('GET', "v1/shipments/{$trackingNumber}/tracking");

            return $response;
        } catch (GuzzleException $e) {
            Log::error('InPost API error getting tracking', [
                'error' => $e->getMessage(),
                'tracking_number' => $trackingNumber,
            ]);
            throw $e;
        }
    }

    /**
     * Get available parcel lockers
     */
    public function getParcelLockers(): array
    {
        try {
            $response = $this->request('GET', 'v1/points');

            return $response;
        } catch (GuzzleException $e) {
            Log::error('InPost API error getting parcel lockers', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Make HTTP request to InPost API
     */
    private function request(string $method, string $uri, array $options = []): array|string
    {
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
        ]);

        $response = $this->http->request($method, $this->baseUrl . '/' . $uri, $options);
        
        $contentType = $response->getHeader('Content-Type')[0] ?? '';
        
        if (str_contains($contentType, 'application/pdf')) {
            return $response->getBody()->getContents();
        }
        
        return json_decode($response->getBody()->getContents(), true);
    }
}
