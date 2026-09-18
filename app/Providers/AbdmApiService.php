<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AbdmApiService
{
    private $publicKeyApiUrl = "https://healthidsbx.abdm.gov.in/api/v1/auth/cert";
    private $publicKeyPath = "abdm_public.pem"; // Store in storage/app/

    private $sessionApiUrl = "https://dev.abdm.gov.in/api/hiecm/gateway/v3/sessions";
    private $otpRequestApiUrl = "https://abhasbx.abdm.gov.in/abha/api/v3/enrollment/request/otp";
    private $enrollApiUrl = "https://abhasbx.abdm.gov.in/abha/api/v3/enrollment/enrol/byAadhaar";
    private $profileAccountApiUrl = "https://abhasbx.abdm.gov.in/abha/api/v3/profile/account";

    private $clientId = "SBX_006994";
    private $clientSecret = "86aec8cd-5828-481b-90e5-9b99f53f86e6";

    public function updatePublicKey()
    {
        $response = Http::get($this->publicKeyApiUrl);
        
        if ($response->successful()) {
            Storage::put($this->publicKeyPath, $response->body());
            return "Public key updated successfully!";
        }

        return "Error: Could not retrieve public key.";
    }

    private function generateGuid()
    {
        return (string) \Str::uuid();
    }

    public function getSessionToken()
    {
        $guid = $this->generateGuid();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'REQUEST-ID' => $guid,
            'TIMESTAMP' => now()->toIso8601String(),
            'X-CM-ID' => 'sbx'
        ])->post($this->sessionApiUrl, [
            "clientId" => $this->clientId,
            "clientSecret" => $this->clientSecret,
            "grantType" => "client_credentials"
        ]);

        if ($response->successful()) {
    $responseData = $response->json(); // Get the response as an array

    if (isset($responseData['accessToken'])) {
        return $responseData['accessToken'];
    } else {
        \Log::error('ABDM API Response: Missing accessToken', ['response' => $responseData]);
        return null; // Or handle it as needed
    }
} else {
    \Log::error('ABDM API Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
    return null; // Or handle the failure appropriately
}


        return false;
    }

    public function encryptAadhaar($aadhaarNumber)
    {
        $publicKey = Storage::get($this->publicKeyPath);
        $keyResource = openssl_pkey_get_public($publicKey);

        if (!$keyResource) {
            return false;
        }

        $encrypted = '';
        openssl_public_encrypt($aadhaarNumber, $encrypted, $keyResource, OPENSSL_PKCS1_OAEP_PADDING);

        return base64_encode($encrypted);
    }

    public function requestOtp($aadhaarNumber)
    {
        $sessionToken = $this->getSessionToken();
        if (!$sessionToken) {
            return ['error' => 'Failed to get session token'];
        }

        $encryptedAadhaar = $this->encryptAadhaar($aadhaarNumber);
        if (!$encryptedAadhaar) {
            return ['error' => 'Aadhaar encryption failed'];
        }

        $guid = $this->generateGuid();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'REQUEST-ID' => $guid,
            'TIMESTAMP' => now()->toIso8601String(),
            "Authorization" => "Bearer " . $sessionToken
        ])->post($this->otpRequestApiUrl, [
            "txnId" => "",
            "scope" => ["abha-enrol"],
            "loginHint" => "aadhaar",
            "loginId" => $encryptedAadhaar,
            "otpSystem" => "aadhaar"
        ]);

        return $response->json();
    }

    public function verifyOtp($txnId, $otpValue, $mobile)
    {
        $sessionToken = $this->getSessionToken();
        if (!$sessionToken) {
            return ['error' => 'Failed to get session token'];
        }

        $encryptedOtp = $this->encryptAadhaar($otpValue);
        if (!$encryptedOtp) {
            return ['error' => 'OTP encryption failed'];
        }

        $guid = $this->generateGuid();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'REQUEST-ID' => $guid,
            'TIMESTAMP' => now()->toIso8601String(),
            'Authorization' => "Bearer " . $sessionToken
        ])->post($this->enrollApiUrl, [
            'authData' => [
                'authMethods' => ['otp'],
                'otp' => [
                    'txnId' => $txnId,
                    'otpValue' => $encryptedOtp,
                    'mobile' => $mobile
                ]
            ],
            'consent' => [
                'code' => 'abha-enrollment',
                'version' => '1.4'
            ]
        ]);

        return $response->json();
    }

    public function getProfileAccount($x_token)
    {
        $sessionToken = $this->getSessionToken();
        if (!$sessionToken) {
            return ['error' => 'Failed to get session token'];
        }

        $guid = $this->generateGuid();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-token' => 'Bearer ' . $x_token,
            'REQUEST-ID' => $guid,
            'TIMESTAMP' => now()->toIso8601String(),
            'Authorization' => "Bearer " . $sessionToken
        ])->get($this->profileAccountApiUrl);

        return $response->json();
    }
}
