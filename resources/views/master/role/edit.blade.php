@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EDIT ROLE
   Matching userView, studentList, and dashboard UI guidelines
   ========================================================================== */

.role-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.role-page * {
    box-sizing: border-box;
}
.role-page-layout {
    min-height: calc(100vh - var(--header-height, 60px) - 16px);
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 6px 10px;
}

/* 1. Hero Header */
.role-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    flex-shrink: 0;
}
.role-hero-text {
    display: flex;
    flex-direction: column;
}
.role-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.role-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.role-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Button System */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
    line-height: 1.3;
    white-space: nowrap;
}
.dash-btn-primary {
    background: #0284c7;
    color: #fff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
    color: #fff;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #fff;
    border-color: rgba(255,255,255,.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.18);
    color: #fff;
}

/* Edit Card */
.edit-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    max-width: 520px;
    margin: 10px auto;
    width: 100%;
}
.edit-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.edit-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.edit-card-body {
    padding: 16px;
}

/* Form Controls */
.form-label-custom {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 4px;
}
.role-input-group {
    position: relative;
    display: flex;
    align-items: center;
}
.role-input-icon {
    position: absolute;
    left: 9px;
    color: #64748b;
    font-size: 12px;
    pointer-events: none;
}
.role-form-control {
    width: 100%;
    height: 34px;
    padding: 4px 10px 4px 28px;
    font-size: 12.5px;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease-in-out;
}
.role-form-control:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}
</style>
@endsection

@section('content')
<div class="content-wrapper role-page">
    <div class="role-page-layout">

        {{-- 1. Hero Header --}}
        <div class="role-hero">
            <div class="role-hero-text">
                <span class="role-kicker">{{ __('master.Role') }} &bull; Access Management</span>
                <h1 class="role-title">
                    <i class="fa fa-pencil-square-o mr-1"></i> {{ __('master.Edit Role') }} &bull; {{ $add_pr['name'] ?? '' }}
                </h1>
            </div>

            <div class="role-hero-actions">
                <a href="{{ url('role_add') }}" class="dash-btn dash-btn-outline" title="Back to Roles Matrix">
                    <i class="fa fa-list mr-1"></i> {{ __('common.View') }} Roles
                </a>
                <a href="{{ url('master_dashboard') }}" class="dash-btn dash-btn-outline" title="Back to Dashboard">
                    <i class="fa fa-arrow-left mr-1"></i> {{ __('common.Back') }}
                </a>
            </div>
        </div>

        {{-- 2. Edit Form Card --}}
        <div class="edit-card">
            <div class="edit-card-header">
                <h3 class="edit-card-title">
                    <i class="fa fa-pencil text-primary"></i> Modify Role Information
                </h3>
                <span class="badge badge-secondary" style="font-size: 11px; padding: 3px 7px;">
                    Role ID: #{{ $add_pr['id'] ?? '' }}
                </span>
            </div>

            <div class="edit-card-body">
                <form id="form-submit-edit" action="{{ url('role_Edit') }}/{{ $add_pr['id'] ?? '' }}" method="post">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label-custom" for="role">
                            {{ __('master.Role') }} Name <span class="text-danger">*</span>
                        </label>
                        <div class="role-input-group">
                            <i class="fa fa-user-circle-o role-input-icon"></i>
                            <input type="text" 
                                   class="role-form-control @error('role') is-invalid @enderror" 
                                   id="role" 
                                   name="role" 
                                   placeholder="Enter role name" 
                                   value="{{ old('role', $add_pr['name'] ?? '') }}" 
                                   required 
                                   autofocus>
                        </div>
                        @error('role')
                            <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2">
                        <a href="{{ url('role_add') }}" class="dash-btn dash-btn-outline text-dark" style="border: 1px solid #cbd5e1; background: #fff;">
                            <i class="fa fa-times mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="dash-btn dash-btn-primary btn-submit" style="padding: 6px 16px; font-size: 12px;">
                            <i class="fa fa-check mr-1"></i> {{ __('common.Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection