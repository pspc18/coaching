@extends('layout.mobile_app')

@section('styles')
<style>
.att-mob-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.att-mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-mob-qr-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 30px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    margin-bottom: 12px;
}
.qr-mob-icon {
    font-size: 52px;
    color: #0284c7;
    margin-bottom: 12px;
}
.qr-mob-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 6px;
}
.qr-mob-desc {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.5;
    max-width: 320px;
    margin: 0 auto 16px;
}
.btn-qr-scan-station {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #0284c7;
    color: #ffffff !important;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    text-decoration: none !important;
}
</style>
@endsection

@section('content')
<div class="att-mob-hero">
    <div class="att-mob-hero-title">
        <i class="fa fa-qrcode text-primary"></i> QR Code Attendance Portal
    </div>
</div>

<div class="att-mob-qr-card">
    <div class="qr-mob-icon"><i class="fa fa-qrcode"></i></div>
    <div class="qr-mob-title">QR Attendance Mode Active</div>
    <div class="qr-mob-desc">
        Contactless QR scanning is enabled. Use scanner device or dedicated scanning station to record real-time check-ins.
    </div>
    <a href="{{ url('qrcode_attendance') }}" class="btn-qr-scan-station">
        <i class="fa fa-camera mr-1"></i> Open QR Scanner
    </a>
</div>
@endsection