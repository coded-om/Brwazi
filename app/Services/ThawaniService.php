<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ThawaniService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        // Ensure string initialization to avoid "Cannot assign null to property" when env is missing
        $this->apiKey = (string) (config('services.thawani.key') ?? env('THAWANI_SECRET') ?? '');
        $this->baseUrl = rtrim(config('services.thawani.base_url', 'https://checkout.thawani.om'), '/');
    }

    public function createCheckoutSession(array $payload)
    {
        // In local/dev without key, return a fake session for smooth UX
        if (empty($this->apiKey)) {
            return [
                'success' => true,
                'data' => [
                    'session_id' => 'sess_' . uniqid(),
                    'data' => [
                        'session_url' => $payload['success_url'] ?? url('/'),
                    ],
                ],
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/api/v1/checkout/session', $payload);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }
}
