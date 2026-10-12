<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PhonePeService
{
    protected string $env;
    protected string $merchantId;
    protected string $saltKey;
    protected string $saltIndex;
    protected string $clientId;
    protected string $clientSecret;
    protected string $clientVersion;
    protected string $baseUrl;

    public function __construct()
    {
        $this->env = config('services.phonepe.env', env('PHONEPE_ENV', 'UAT'));
        $this->merchantId = config('services.phonepe.merchant_id', env('PHONEPE_MERCHANT_ID', 'PGTESTPAYUAT86'));
        $this->saltKey = config('services.phonepe.salt_key', env('PHONEPE_SALT_KEY', '96434309-7796-489d-8924-ab56988a6076'));
        $this->saltIndex = (string) config('services.phonepe.salt_index', env('PHONEPE_SALT_INDEX', '1'));
        $this->clientId = config('services.phonepe.client_id', env('PHONEPE_CLIENT_ID', ''));
        $this->clientSecret = config('services.phonepe.client_secret', env('PHONEPE_CLIENT_SECRET', ''));
        $this->clientVersion = (string) config('services.phonepe.client_version', env('PHONEPE_CLIENT_VERSION', '1'));

        if (strtoupper($this->env) === 'PRODUCTION') {
            $this->baseUrl = config('services.phonepe.prod_base_url', 'https://api.phonepe.com/apis/pg');
        } else {
            $this->baseUrl = config('services.phonepe.uat_base_url', 'https://api-preprod.phonepe.com/apis/pg-sandbox');
        }
    }

    /**
     * Check if valid real PhonePe credentials (either SHA256 Salt or OAuth) are provided
     */
    public function hasValidCredentials(): bool
    {
        // Standard Checksum (Salt Key) mode
        if (!empty($this->merchantId) && !empty($this->saltKey)) {
            return true;
        }

        // OAuth mode
        return !empty($this->clientId) 
            && !empty($this->clientSecret) 
            && $this->clientId !== 'SANDBOX_CLIENT_ID' 
            && $this->clientSecret !== 'SANDBOX_CLIENT_SECRET';
    }

    /**
     * Step 1: Generate Authorization Token (OAuth 2.0 - if OAuth credentials provided)
     */
    public function getAuthToken(): ?string
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return null;
        }

        $cacheKey = 'phonepe_oauth_token_' . md5($this->clientId);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
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
     * Step 2: Create Payment Request
     * Uses official PhonePe pg/v1/pay API (with standard UAT or Live Salt)
     * Opens the real PhonePe Sandbox / Live Checkout Gateway URL
     */
    public function createPaymentOrder(string $merchantOrderId, float $amountRupees, string $redirectUrl, array $metaInfo = []): array
    {
        $amountPaise = (int) round($amountRupees * 100);

        // 1. If OAuth credentials are configured (PhonePe OAuth v2 Standard Checkout - Official UAT & Live)
        if (!empty($this->clientId) && !empty($this->clientSecret)) {
            $oauthRes = $this->createOrderViaOAuth($merchantOrderId, $amountPaise, $redirectUrl, $metaInfo);
            if (!empty($oauthRes['success'])) {
                return $oauthRes;
            }
        }

        // 2. If standard checksum salt key is available (SHA256 PG v1)
        if (!empty($this->merchantId) && !empty($this->saltKey)) {
            return $this->createOrderViaChecksum($merchantOrderId, $amountPaise, $redirectUrl, $metaInfo);
        }

        // 3. Fallback to simulator only if absolutely no credentials exist
        return [
            'success' => true,
            'redirect_url' => url('/student/payment/phonepe-simulator?order_id=' . urlencode($merchantOrderId)),
            'merchant_order_id' => $merchantOrderId,
            'phonepe_order_id' => 'SIM_' . time(),
            'state' => 'PENDING',
            'is_simulation' => true,
        ];
    }

    /**
     * Standard PhonePe pg/v1/pay initiation with X-VERIFY checksum (Official UAT & Production)
     */
    protected function createOrderViaChecksum(string $merchantOrderId, int $amountPaise, string $redirectUrl, array $metaInfo = []): array
    {
        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $merchantOrderId,
            'merchantUserId' => 'STUDENT_' . ($metaInfo['udf1'] ?? '1'),
            'amount' => $amountPaise,
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $redirectUrl,
            'mobileNumber' => '9999999999',
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $encode = base64_encode(json_encode($payload));
        $sha256 = hash('sha256', $encode . '/pg/v1/pay' . $this->saltKey);
        $xVerify = $sha256 . '###' . $this->saltIndex;

        try {
            $url = $this->baseUrl . '/pg/v1/pay';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
            ])
            ->timeout(20)
            ->post($url, [
                'request' => $encode,
            ]);

            $resData = $response->json();

            if ($response->successful() && !empty($resData['success'])) {
                $gatewayUrl = $resData['data']['instrumentResponse']['redirectInfo']['url'] ?? null;
                $phonepeTxnId = $resData['data']['merchantTransactionId'] ?? $merchantOrderId;

                if ($gatewayUrl) {
                    return [
                        'success' => true,
                        'redirect_url' => $gatewayUrl,
                        'merchant_order_id' => $merchantOrderId,
                        'phonepe_order_id' => $phonepeTxnId,
                        'state' => 'PENDING',
                        'response' => $resData,
                        'is_simulation' => false,
                    ];
                }
            }

            Log::error('PhonePe Checksum Order Failed', [
                'merchant' => $this->merchantId,
                'status' => $response->status(),
                'response' => $resData ?? $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $resData['message'] ?? 'Unable to initiate PhonePe payment request.',
                'raw_response' => $resData,
            ];
        } catch (\Exception $e) {
            Log::error('PhonePe Checksum Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection error with PhonePe server: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * PhonePe OAuth v2 initiation (/checkout/v2/pay)
     */
    protected function createOrderViaOAuth(string $merchantOrderId, int $amountPaise, string $redirectUrl, array $metaInfo = []): array
    {
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
            'expireAfter' => 1200,
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

            return [
                'success' => false,
                'message' => $resData['message'] ?? 'Unable to initiate PhonePe payment request.',
                'raw_response' => $resData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error with PhonePe server: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Step 4: Check Payment Status
     * Queries PhonePe official server-to-server API
     */
    public function checkPaymentStatus(string $merchantOrderId): array
    {
        // If simulation order
        if (str_starts_with($merchantOrderId, 'SIM_')) {
            return [
                'success' => true,
                'state' => 'COMPLETED',
                'transaction_id' => 'TXN_SIM_' . time(),
                'message' => 'Simulation Payment Successful',
            ];
        }

        // 1. OAuth Status Check (Primary when OAuth credentials configured)
        if (!empty($this->clientId) && !empty($this->clientSecret)) {
            $token = $this->getAuthToken();
            if ($token) {
                try {
                    $url = $this->baseUrl . '/checkout/v2/order/' . urlencode($merchantOrderId) . '/status';
                    $response = Http::withHeaders([
                        'Authorization' => 'O-Bearer ' . $token,
                    ])
                    ->timeout(15)
                    ->get($url);

                    $resData = $response->json();
                    $state = $resData['payload']['state'] ?? $resData['state'] ?? $resData['data']['state'] ?? 'FAILED';
                    $transactionId = $resData['payload']['transactionId'] ?? $resData['transactionId'] ?? $resData['data']['transactionId'] ?? null;

                    return [
                        'success' => ($state === 'COMPLETED'),
                        'state' => $state,
                        'transaction_id' => $transactionId,
                        'raw_response' => $resData,
                    ];
                } catch (\Exception $e) {
                    Log::error('PhonePe OAuth Status Check Exception: ' . $e->getMessage());
                }
            }
        }

        // 2. Standard Checksum Status Check (/pg/v1/status/{merchantId}/{merchantTransactionId})
        if (!empty($this->merchantId) && !empty($this->saltKey)) {
            $endpoint = "/pg/v1/status/{$this->merchantId}/{$merchantOrderId}";
            $sha256 = hash('sha256', $endpoint . $this->saltKey);
            $xVerify = $sha256 . '###' . $this->saltIndex;

            try {
                $url = $this->baseUrl . $endpoint;
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $xVerify,
                    'X-MERCHANT-ID' => $this->merchantId,
                ])
                ->timeout(15)
                ->get($url);

                $resData = $response->json();
                $code = $resData['code'] ?? null;
                $data = $resData['data'] ?? [];
                $state = $data['state'] ?? ($code === 'PAYMENT_SUCCESS' ? 'COMPLETED' : 'FAILED');
                $transactionId = $data['transactionId'] ?? null;
                $isSuccess = ($code === 'PAYMENT_SUCCESS' || $state === 'COMPLETED');

                return [
                    'success' => $isSuccess,
                    'state' => $state,
                    'transaction_id' => $transactionId,
                    'raw_response' => $resData,
                    'message' => $resData['message'] ?? 'Payment processed',
                ];
            } catch (\Exception $e) {
                Log::error('PhonePe Checksum Status Check Exception: ' . $e->getMessage());
                return [
                    'success' => false,
                    'state' => 'FAILED',
                    'message' => 'Status check error: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'success' => false,
            'state' => 'FAILED',
            'message' => 'No active payment credentials configured to verify status.',
        ];
    }
}

// Bi-directional class aliases for full case-insensitivity on all environments (Linux/Windows)
if (!class_exists('app\Services\PhonePeService', false)) {
    class_alias('App\Services\PhonePeService', 'app\Services\PhonePeService');
}
if (!class_exists('App\Services\PhonePeService', false)) {
    class_alias('app\Services\PhonePeService', 'App\Services\PhonePeService');
}
