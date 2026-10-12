<?php

namespace app\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PhonePeService
{
    protected string $env;
    protected string $merchantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $clientVersion;
    protected string $baseUrl;

    public function __construct()
    {
        $this->env = config('services.phonepe.env', env('PHONEPE_ENV', 'UAT'));
        $this->merchantId = config('services.phonepe.merchant_id', env('PHONEPE_MERCHANT_ID', ''));
        $this->clientId = config('services.phonepe.client_id', env('PHONEPE_CLIENT_ID', ''));
        $this->clientSecret = config('services.phonepe.client_secret', env('PHONEPE_CLIENT_SECRET', ''));
        $this->clientVersion = config('services.phonepe.client_version', env('PHONEPE_CLIENT_VERSION', '1'));

        if (strtoupper($this->env) === 'PRODUCTION') {
            $this->baseUrl = config('services.phonepe.prod_base_url', 'https://api.phonepe.com/apis/pg');
        } else {
            $this->baseUrl = config('services.phonepe.uat_base_url', 'https://api-preprod.phonepe.com/apis/pg-sandbox');
        }
    }

    /**
     * Check if valid real PhonePe credentials are provided
     */
    public function hasValidCredentials(): bool
    {
        return !empty($this->clientId) 
            && !empty($this->clientSecret) 
            && $this->clientId !== 'SANDBOX_CLIENT_ID' 
            && $this->clientSecret !== 'SANDBOX_CLIENT_SECRET';
    }

    /**
     * Step 1: Generate Authorization Token (OAuth 2.0)
     */
    public function getAuthToken(): ?string
    {
        $cacheKey = 'phonepe_oauth_token_' . md5($this->clientId);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // If credentials are mock/placeholder, return mock token
        if (!$this->hasValidCredentials()) {
            $mockToken = 'MOCK_PHONEPE_TOKEN_' . time();
            Cache::put($cacheKey, $mockToken, 1800);
            return $mockToken;
        }

        try {
            $url = $this->baseUrl . '/v1/oauth/token';
            $response = Http::asForm()
                ->timeout(15)
                ->post($url, [
                    'client_id' => $this->clientId,
                    'client_version' => $this->clientVersion,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = max(60, ($data['expires_in'] ?? 3600) - 120);

                if ($token) {
                    Cache::put($cacheKey, $token, $expiresIn);
                    return $token;
                }
            }

            Log::error('PhonePe OAuth Token Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('PhonePe OAuth Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 2: Create Payment Request (/checkout/v2/pay)
     */
    public function createPaymentOrder(string $merchantOrderId, float $amountRupees, string $redirectUrl, array $metaInfo = []): array
    {
        $amountPaise = (int) round($amountRupees * 100);

        // If credentials are not real, route to internal PhonePe Sandbox Simulator page
        if (!$this->hasValidCredentials()) {
            return [
                'success' => true,
                'redirect_url' => url('/student/payment/phonepe-simulator?order_id=' . urlencode($merchantOrderId)),
                'merchant_order_id' => $merchantOrderId,
                'phonepe_order_id' => 'SIM_' . time(),
                'state' => 'PENDING',
                'is_simulation' => true,
            ];
        }

        $token = $this->getAuthToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to obtain PhonePe authorization token. Please verify client credentials.',
            ];
        }

        $payload = [
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amountPaise,
            'expireAfter' => 1200, // 20 minutes
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ],
            'metaInfo' => (object) $metaInfo,
        ];

        try {
            $url = $this->baseUrl . '/checkout/v2/pay';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'O-Bearer ' . $token,
            ])
            ->timeout(20)
            ->post($url, $payload);

            $resData = $response->json();

            if ($response->successful()) {
                $redirectUrl = $resData['redirectUrl'] ?? $resData['data']['redirectUrl'] ?? null;
                $phonepeOrderId = $resData['orderId'] ?? $resData['data']['orderId'] ?? null;
                $state = $resData['state'] ?? $resData['data']['state'] ?? 'PENDING';

                if ($redirectUrl) {
                    return [
                        'success' => true,
                        'redirect_url' => $redirectUrl,
                        'merchant_order_id' => $merchantOrderId,
                        'phonepe_order_id' => $phonepeOrderId,
                        'state' => $state,
                        'response' => $resData,
                        'is_simulation' => false,
                    ];
                }
            }

            Log::error('PhonePe Create Order Failed', [
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $resData['message'] ?? 'Unable to initiate PhonePe payment request.',
                'raw_response' => $resData,
            ];
        } catch (\Exception $e) {
            Log::error('PhonePe Create Order Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection error with PhonePe server: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Step 4: Check Payment Status (/checkout/v2/order/{merchantOrderId}/status)
     */
    public function checkPaymentStatus(string $merchantOrderId): array
    {
        // If simulation order
        if (str_starts_with($merchantOrderId, 'SIM_') || !$this->hasValidCredentials()) {
            return [
                'success' => true,
                'state' => 'COMPLETED',
                'transaction_id' => 'TXN_SIM_' . time(),
                'message' => 'Simulation Payment Successful',
            ];
        }

        $token = $this->getAuthToken();
        if (!$token) {
            return [
                'success' => false,
                'state' => 'FAILED',
                'message' => 'Unable to verify payment status: Auth token failure',
            ];
        }

        try {
            $url = $this->baseUrl . '/checkout/v2/order/' . urlencode($merchantOrderId) . '/status';
            $response = Http::withHeaders([
                'Authorization' => 'O-Bearer ' . $token,
            ])
            ->timeout(15)
            ->get($url);

            $resData = $response->json();

            // As specified in PhonePe docs: rely on root or payload.state
            $state = $resData['payload']['state'] ?? $resData['state'] ?? $resData['data']['state'] ?? 'FAILED';
            $transactionId = $resData['payload']['transactionId'] ?? $resData['transactionId'] ?? $resData['data']['transactionId'] ?? null;

            return [
                'success' => ($state === 'COMPLETED'),
                'state' => $state,
                'transaction_id' => $transactionId,
                'raw_response' => $resData,
            ];
        } catch (\Exception $e) {
            Log::error('PhonePe Status Check Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'state' => 'FAILED',
                'message' => 'Status check error: ' . $e->getMessage(),
            ];
        }
    }
}

// Bi-directional class aliases for full case-insensitivity on all environments (Linux/Windows)
if (!class_exists('App\Services\PhonePeService', false)) {
    class_alias('app\Services\PhonePeService', 'App\Services\PhonePeService');
}
if (!class_exists('app\Services\PhonePeService', false)) {
    class_alias('App\Services\PhonePeService', 'app\Services\PhonePeService');
}
