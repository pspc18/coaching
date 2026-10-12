@extends('student_login.layout.app')

@section('title', 'Payment Failed')

@section('content')
<div class="content-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px;">
    <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 480px; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;">
        
        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; text-align: center; padding: 32px 20px;">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <i class="fa fa-times" style="font-size: 34px; color: #ffffff;"></i>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 4px;">Payment Failed / Cancelled</h2>
            <p style="font-size: 13px; opacity: 0.95;">No amount was deducted from your account.</p>
        </div>

        <div style="padding: 24px;">
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #991b1b; font-weight: 600;">Status:</span>
                    <span style="color: #dc2626; font-weight: 800;">DECLINED / CANCELLED</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Order Ref:</span>
                    <span style="color: #0f172a; font-weight: 600;">{{ $order->merchant_order_id }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: #64748b;">Reason:</span>
                    <span style="color: #b91c1c; font-weight: 600; text-align: right; max-width: 60%;">{{ $reason ?? 'Payment process was interrupted or declined.' }}</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ url('student/dashboard') }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #002C54; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                    <i class="fa fa-refresh"></i> Try Again from Dashboard
                </a>
                <a href="{{ url('fees_history') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; background: #f1f5f9; color: #475569; padding: 11px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;">
                    View Fees Details
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
