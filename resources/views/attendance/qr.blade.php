@extends('layout.app')

@php
    $attendanceBaseUrl = request()->url();
@endphp

@section('styles')
<style>
.att-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.att-layout {
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height, 56px) - 16px);
    overflow: hidden;
    gap: 6px;
}
.att-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    flex-shrink: 0;
}
.att-hero-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    font-weight: 600;
}
.att-hero-title {
    font-size: 15px;
    font-weight: 700;
    margin: 1px 0 0;
    line-height: 1.2;
    color: #ffffff;
}
.att-hero-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}
.att-card-placeholder {
    flex: 1;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    text-align: center;
}
.qr-icon {
    font-size: 48px;
    color: #0284c7;
    margin-bottom: 12px;
}
.qr-title {
    font-size: 16px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 6px;
}
.qr-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 420px;
    line-height: 1.5;
}
</style>
@endsection

@section('content')
<div class="content-wrapper att-page-wrapper">
    <section class="content p-2">
        <div class="container-fluid p-0">
            <div class="att-layout">
                <div class="att-hero">
                    <div>
                        <span class="att-hero-kicker"><i class="fa fa-qrcode mr-1"></i> Attendance Management</span>
                        <h1 class="att-hero-title">QR Code Attendance Portal</h1>
                        <p class="att-hero-subtitle">Fast contactless QR attendance scanning station</p>
                    </div>
                </div>

                <div class="att-card-placeholder">
                    <div class="qr-icon"><i class="fa fa-qrcode"></i></div>
                    <h3 class="qr-title">QR Attendance Mode Active</h3>
                    <p class="qr-desc">
                        QR attendance scanning station will automatically capture student and staff check-in/out through dynamic biometric and barcode scanners.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
