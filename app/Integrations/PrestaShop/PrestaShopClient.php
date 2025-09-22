<?php

namespace App\Integrations\PrestaShop;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PrestaShopClient
{
    private Client $http;
    private string $baseUrl;
    private string $apiKey;

    public function __construct(Client $http, string $baseUrl, string $apiKey)
    {
        $this->http = $http;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
    }

    /**
     * Get orders from PrestaShop
     */
    public function getOrders(?Carbon $since = null, int $page = 1, int $perPage = 100): array
    {
        try {
            $params = [
                'display' => 'full',
                'limit' => $perPage,
                'offset' => ($page - 1) * $perPage,
            ];

            if ($since) {
                $params['filter[date_upd]'] = '[' . $since->format('Y-m-d H:i:s') . ']';
            }

            $response = $this->request('GET', 'orders', [
                'query' => $params,
            ]);

            return $response['orders'] ?? [];
        } catch (GuzzleException $e) {
            Log::error('PrestaShop API error getting orders', [
                'error' => $e->getMessage(),
                'since' => $since,
                'page' => $page,
            ]);
            throw $e;
        }
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $orderId): ?array
    {
        try {
            $response = $this->request('GET', "orders/{$orderId}", [
                'query' => ['display' => 'full'],
            ]);

            return $response['order'] ?? null;
        } catch (GuzzleException $e) {
            Log::error('PrestaShop API error getting order', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return null;
        }
    }

    /**
     * Get customers from PrestaShop
     */
    public function getCustomers(?Carbon $since = null, int $page = 1, int $perPage = 100): array
    {
        try {
            $params = [
                'display' => 'full',
                'limit' => $perPage,
                'offset' => ($page - 1) * $perPage,
            ];

            if ($since) {
                $params['filter[date_upd]'] = '[' . $since->format('Y-m-d H:i:s') . ']';
            }

            $response = $this->request('GET', 'customers', [
                'query' => $params,
            ]);

            return $response['customers'] ?? [];
        } catch (GuzzleException $e) {
            Log::error('PrestaShop API error getting customers', [
                'error' => $e->getMessage(),
                'since' => $since,
                'page' => $page,
            ]);
            throw $e;
        }
    }

    /**
     * Get products from PrestaShop
     */
    public function getProducts(?Carbon $since = null, int $page = 1, int $perPage = 100): array
    {
        try {
            $params = [
                'display' => 'full',
                'limit' => $perPage,
                'offset' => ($page - 1) * $perPage,
            ];

            if ($since) {
                $params['filter[date_upd]'] = '[' . $since->format('Y-m-d H:i:s') . ']';
            }

            $response = $this->request('GET', 'products', [
                'query' => $params,
            ]);

            return $response['products'] ?? [];
        } catch (GuzzleException $e) {
            Log::error('PrestaShop API error getting products', [
                'error' => $e->getMessage(),
                'since' => $since,
                'page' => $page,
            ]);
            throw $e;
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        try {
            $this->request('PUT', "orders/{$orderId}", [
                'json' => [
                    'order' => [
                        'current_state' => $status,
                    ],
                ],
            ]);

            return true;
        } catch (GuzzleException $e) {
            Log::error('PrestaShop API error updating order status', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'status' => $status,
            ]);
            return false;
        }
    }

    /**
     * Make HTTP request to PrestaShop API
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        $options['auth'] = [$this->apiKey, ''];
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Accept' => 'application/json',
        ]);

        $response = $this->http->request($method, $this->baseUrl . '/api/' . $uri, $options);
        
        return json_decode($response->getBody()->getContents(), true);
    }
}
