@extends('layout.mobile_app')

@section('title', 'Mobile View Not Supported')

@php
    $setting = Helper::getSetting();
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE VIEW NOT SUPPORTED NOTICE
   Simple, clean notice with exclamation icon & high contrast
   ========================================================================== */

.mob-unsupported-wrap {
    padding: 30px 16px 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
}

.mob-unsupported-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 36px 20px;
    width: 100%;
    max-width: 390px;
    box-shadow: 0 4px 16px rgba(0, 44, 84, 0.06);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.mob-exclamation-badge {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #fffbeb;
    border: 2px solid #fde68a;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    color: #d97706;
    font-size: 30px;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.12);
}

.mob-unsupported-title {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px 0;
    line-height: 1.3;
}

.mob-unsupported-text {
    font-size: 13px;
    color: #475569;
    line-height: 1.6;
    margin: 0;
    max-width: 320px;
}

.mob-unsupported-text strong {
    color: #0f172a;
    font-weight: 700;
}
</style>
@endsection

@section('content')
<div class="mob-unsupported-wrap">
    <div class="mob-unsupported-card">
        <div class="mob-exclamation-badge">
            <i class="fa fa-exclamation-triangle"></i>
        </div>

        <h2 class="mob-unsupported-title">Mobile View Not Supported</h2>

        <p class="mob-unsupported-text">
            This page is not supported on mobile view. To access and use this feature, please open it on a desktop computer.
        </p>
    </div>
</div>
@endsection
