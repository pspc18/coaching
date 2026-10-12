@extends('student_login.layout.app')

@section('title', 'Payment Successful')

@section('content')
<div class="content-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px;">
    <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 500px; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;">
        
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-align: center; padding: 32px 20px;">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <i class="fa fa-check" style="font-size: 34px; color: #ffffff;"></i>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 4px;">Payment Successful!</h2>
            <p style="font-size: 13px; opacity: 0.95;">Fees have been recorded and settled in the system.</p>
        </div>

        <div style="padding: 24px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                    <span style="color: #64748b; font-weight: 500;">Amount Paid:</span>
                    <span style="color: #0f172a; font-weight: 800; font-size: 18px;">₹ {{ number_format($order->amount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Transaction ID:</span>
                    <span style="color: #0f172a; font-weight: 700;">{{ $order->phonepe_transaction_id ?? $order->merchant_order_id }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Order Ref:</span>
                    <span style="color: #0f172a; font-weight: 600;">{{ $order->merchant_order_id }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Payment Mode:</span>
                    <span style="color: #5f259f; font-weight: 700;"><i class="fa fa-shield"></i> PhonePe UPI</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: #64748b;">Date &amp; Time:</span>
                    <span style="color: #0f172a; font-weight: 600;">{{ date('d-M-Y, h:i A', strtotime($order->updated_at)) }}</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if(!empty($order->invoice_id))
                    <a href="{{ url('fees_receipt/' . $order->invoice_id) }}" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #002C54; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.2s;">
                        <i class="fa fa-file-text-o"></i> View / Download Fee Receipt
                    </a>
                @endif
                <a href="{{ url('fees_history') }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                    <i class="fa fa-history"></i> Check Fees History
                </a>
                <a href="{{ url('student/dashboard') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; background: #f1f5f9; color: #475569; padding: 11px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; margin-top: 4px;">
                    Return to Student Dashboard
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
