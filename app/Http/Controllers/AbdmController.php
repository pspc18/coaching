<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\AbdmApiService;
use Illuminate\Support\Facades\Session;

class AbdmController extends Controller
{
    protected $abdmService;

    public function __construct(AbdmApiService $abdmService)
    {
        $this->abdmService = $abdmService;
    }

    public function updateKey()
    {
        return response()->json($this->abdmService->updatePublicKey());
    }

    public function sendOtp(Request $request)
    {
        $aadhaarNumber = $request->input('aadhaar');
        Session::put('ABDM.aadhar_number', $aadhaarNumber);
        $response = $this->abdmService->requestOtp($aadhaarNumber);

        if (!empty($response['txnId'])) {
            Session::put('ABDM.txnId', $response['txnId']);
            return redirect()->route('verifyOtp');
        }

        return response()->json(['error' => 'Failed to send OTP'], 400);
    }

    public function verifyOtp(Request $request)
    {
        $txnId = Session::get('ABDM.txnId');
        $otpValue = $request->input('otp_value');
        $mobile = $request->input('mobile_number');

        $response = $this->abdmService->verifyOtp($txnId, $otpValue, $mobile);

        if (!empty($response['tokens']['token'])) {
            Session::put('ABDM.X-token', $response['tokens']['token']);
            return redirect()->route('profile');
        }

        return response()->json(['error' => 'OTP verification failed'], 400);
    }

    public function getProfile()
    {
        $x_token = Session::get('ABDM.X-token');
        $response = $this->abdmService->getProfileAccount($x_token);

        return response()->json($response);
    }
}
