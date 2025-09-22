<?php

namespace App\Integrations\SMSAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SMSApiClient
{
    private Client $http;
    private string $baseUrl;
    private string $token;

    public function __construct(Client $http, string $baseUrl, string $token)
    {
        $this->http = $http;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }

    /**
     * Send SMS
     */
    public function sendSms(string $phone, string $message, ?string $from = null): array
    {
        try {
            $payload = [
                'to' => $phone,
                'message' => $message,
            ];

            if ($from) {
                $payload['from'] = $from;
            }

            $response = $this->request('POST', 'sms.do', [
                'form_params' => $payload,
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('SMSAPI error sending SMS', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'message' => $message,
            ]);
            throw $e;
        }
    }

    /**
     * Send multiple SMS messages
     */
    public function sendBulkSms(array $messages): array
    {
        try {
            $response = $this->request('POST', 'sms.do', [
                'form_params' => [
                    'messages' => $messages,
                ],
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('SMSAPI error sending bulk SMS', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages),
            ]);
            throw $e;
        }
    }

    /**
     * Get SMS status
     */
    public function getSmsStatus(string $smsId): array
    {
        try {
            $response = $this->request('GET', 'sms.do', [
                'query' => [
                    'id' => $smsId,
                ],
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('SMSAPI error getting SMS status', [
                'error' => $e->getMessage(),
                'sms_id' => $smsId,
            ]);
            throw $e;
        }
    }

    /**
     * Get account balance
     */
    public function getBalance(): array
    {
        try {
            $response = $this->request('GET', 'user.do', [
                'query' => [
                    'credits' => 1,
                ],
            ]);

            return $response;
        } catch (GuzzleException $e) {
            Log::error('SMSAPI error getting balance', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Make HTTP request to SMSAPI
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        $options['query'] = array_merge($options['query'] ?? [], [
            'username' => $this->token,
            'password' => $this->token,
        ]);

        $response = $this->http->request($method, $this->baseUrl . '/' . $uri, $options);
        
        return json_decode($response->getBody()->getContents(), true);
    }
}
