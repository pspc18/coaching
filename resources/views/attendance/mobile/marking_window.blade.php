@php
    $classes = collect($classes ?? []);
    $windows = collect($windows ?? []);
    $users = collect($users ?? []);
    $userWindows = collect($userWindows ?? []);
    $timeValue = fn($value) => !empty($value) ? substr((string)$value, 0, 5) : '';
    $classConfigured = $classConfigured ?? $windows->count();
    $userConfigured = $userConfigured ?? $userWindows->count();
    $classActiveCount = $classActiveCount ?? $windows->where('is_active', 1)->count();
    $userActiveCount = $userActiveCount ?? $userWindows->where('is_active', 1)->count();
    $activeItemsCount = $activeTab === 'classes' ? $classes->count() : $users->count();
    $isUserTab = ($activeTab === 'users');
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE ATTENDANCE MARKING WINDOW
   ========================================================================== */

/* 1. Dark Navy Hero Banner */
.mob-win-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-win-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-win-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.mob-win-badge-mode {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    background: rgba(56, 189, 248, 0.2);
    border: 1px solid rgba(56, 189, 248, 0.4);
    color: #38bdf8;
    text-transform: uppercase;
}

/* KPI in Hero */
.mob-win-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
}
.mob-win-metric-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 3px;
    padding: 4px 2px;
    text-align: center;
}
.mob-win-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-win-metric-val {
    font-size: 13px;
    font-weight: 800;
    line-height: 1;
}
.win-val-total { color: #38bdf8; }
.win-val-active { color: #4ade80; }
.win-val-cfg { color: #cbd5e1; }

/* 2. Navigation Tabs (Classes vs Users) */
.mob-tabs-bar {
    display: flex;
    background: #002C54;
    border-radius: 4px;
    padding: 3px;
    gap: 4px;
    margin-bottom: 8px;
    border: 1px solid #001f3d;
}
.mob-tab-btn {
    flex: 1;
    text-align: center;
    padding: 6px 4px;
    font-size: 11px;
    font-weight: 700;
    color: #cbd5e1;
    text-decoration: none !important;
    border-radius: 3px;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.mob-tab-btn.active {
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,.25);
}

/* 3. Search & Bulk Toolbar Card */
.mob-tool-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.mob-search-input-wrap {
    position: relative;
    margin-bottom: 6px;
}
.mob-search-input-wrap i {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding-left: 26px;
    padding-right: 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    outline: none;
    transition: border-color .15s;
}
.mob-search-input:focus {
    border-color: #002C54;
    background: #ffffff;
}

/* Bulk Bar */
.mob-bulk-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-wrap: wrap;
    background: #f1f5f9;
    padding: 6px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
}
.mob-bulk-times {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-bulk-time-input {
    height: 26px;
    width: 78px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 4px;
    text-align: center;
    color: #0f172a;
    outline: none;
    background: #ffffff;
}
.btn-bulk-apply {
    height: 26px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 700;
    background: #0284c7;
    border: 1px solid #0284c7;
    color: #ffffff;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-bulk-shortcuts {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-quick-toggle {
    height: 24px;
    padding: 0 6px;
    font-size: 9.5px;
    font-weight: 700;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
}
.btn-toggle-enable {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #047857;
}
.btn-toggle-disable {
    background: #fef2f2;
    border-color: #fecaca;
    color: #b91c1c;
}

/* 4. Marking Window Cards */
.mob-card-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-bottom: 60px; /* Space for bottom fixed save bar */
}
.mob-win-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    transition: all .15s;
}
.mob-win-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-win-card-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-role-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    background: #e2e8f0;
    color: #334155;
    border: 1px solid #cbd5e1;
}

/* Custom Status Switch */
.win-switch-wrap {
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    margin: 0;
}
.win-switch-wrap input {
    display: none;
}
.win-switch-pill {
    padding: 2px 7px;
    font-size: 9.5px;
    font-weight: 800;
    border-radius: 3px;
    text-transform: uppercase;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    cursor: pointer;
}
.win-switch-pill.is-active {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}
.win-switch-pill.is-inactive {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* Card Body */
.mob-win-card-body {
    padding: 8px 10px;
}
.mob-time-inputs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 6px;
    align-items: flex-end;
    margin-bottom: 6px;
}
.mob-time-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.mob-time-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 3px;
}
.mob-time-input {
    height: 30px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 2px 6px;
    color: #0f172a;
    outline: none;
    text-align: center;
    background: #ffffff;
    width: 100%;
}
.mob-time-input:focus {
    border-color: #0284c7;
    background: #f0f9ff;
    box-shadow: 0 0 0 1px #0284c7;
}
.mob-duration-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    height: 100%;
}
.mob-duration-pill {
    font-size: 9.5px;
    font-weight: 800;
    padding: 4px 6px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    white-space: nowrap;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Notes Input Row */
.mob-note-row {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-note-input {
    flex: 1;
    height: 26px;
    font-size: 10.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 1px 6px;
    color: #0f172a;
    outline: none;
    background: #f8fafc;
}
.mob-note-input:focus {
    border-color: #002C54;
    background: #ffffff;
}
.btn-card-reset {
    width: 26px;
    height: 26px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #64748b;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    flex-shrink: 0;
}
.btn-card-reset:active {
    background: #002C54;
    color: #ffffff;
}

/* 5. Fixed Bottom Save Toolbar */
.mob-save-toolbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #002C54;
    border-top: 1px solid rgba(255,255,255,0.18);
    box-shadow: 0 -4px 14px rgba(0,0,0,0.2);
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
}
.mob-save-toolbar-info {
    font-size: 10.5px;
    color: #cbd5e1;
    line-height: 1.2;
}
.mob-save-toolbar-info b {
    color: #ffffff;
}
.btn-mob-save {
    height: 32px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 800;
    background: #0284c7;
    border: 1px solid #38bdf8;
    color: #ffffff;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
}
.btn-mob-save:active {
    background: #0369a1;
}

/* 6. Custom Mobile Dialog Bottom Sheet */
.mob-sheet-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.65);
    backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease-in-out;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -6px 25px rgba(0, 44, 84, 0.3);
    z-index: 99995;
    transform: translateY(100%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 500px;
    margin: 0 auto;
    overflow: hidden;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}
.mob-sheet-handle {
    width: 36px;
    height: 4px;
    background: #cbd5e1;
    border-radius: 2px;
    margin: 8px auto 4px;
}
.mob-sheet-header {
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-sheet-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.mob-sheet-icon.is-primary { background: #e0f2fe; color: #0284c7; }
.mob-sheet-icon.is-danger { background: #fee2e2; color: #dc2626; }
.mob-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin: 0;
}
.mob-sheet-body {
    padding: 14px 16px;
    font-size: 12px;
    color: #334155;
    line-height: 1.45;
}
.mob-sheet-footer {
    padding: 10px 16px 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    border-top: 1px solid #f1f5f9;
}
.mob-sheet-btn {
    height: 34px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid transparent;
}
.mob-sheet-btn-cancel {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
}
.mob-sheet-btn-confirm {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

/* Quick Floating Toast */
#mobToast {
    position: fixed;
    top: 14px;
    left: 50%;
    transform: translateX(-50%) translateY(-100px);
    background: #002C54;
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: transform .25s ease-out;
    pointer-events: none;
    border: 1px solid rgba(255,255,255,0.2);
}
#mobToast.show {
    transform: translateX(-50%) translateY(0);
}
</style>
@endsection

@section('content')
<div class="container-fluid px-1 py-1" style="max-width: 560px; margin: 0 auto;">

    {{-- 1. Dark Navy Hero Banner --}}
    <div class="mob-win-hero">
        <div class="mob-win-hero-top">
            <span class="mob-win-title">
                <i class="fa fa-clock-o text-info"></i> Marking Windows
            </span>
            <span class="mob-win-badge-mode">
                <i class="fa {{ $isUserTab ? 'fa-users' : 'fa-graduation-cap' }}"></i> {{ $isUserTab ? 'Staff' : 'Classes' }}
            </span>
        </div>

        {{-- 3 KPI Metrics --}}
        <div class="mob-win-metrics">
            <div class="mob-win-metric-cell">
                <span class="mob-win-metric-tag">Total</span>
                <span class="mob-win-metric-val win-val-total">{{ $activeItemsCount }}</span>
            </div>
            <div class="mob-win-metric-cell">
                <span class="mob-win-metric-tag">Configured</span>
                <span class="mob-win-metric-val win-val-cfg">{{ $isUserTab ? $userConfigured : $classConfigured }}</span>
            </div>
            <div class="mob-win-metric-cell">
                <span class="mob-win-metric-tag">Active</span>
                <span class="mob-win-metric-val win-val-active" id="hero-active-count">{{ $isUserTab ? $userActiveCount : $classActiveCount }}</span>
            </div>
        </div>
    </div>

    {{-- Success & Error Alerts --}}
    @if(session('message'))
        <div class="alert alert-success py-2 mb-2" style="font-size: 11.5px; border-radius: 4px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
        </div>
    @endif
    @if(!empty($errors) && $errors->any())
        <div class="alert alert-danger py-2 mb-2" style="font-size: 11.5px; border-radius: 4px;">
            <i class="fa fa-exclamation-circle mr-1"></i>
            <strong>Please resolve errors:</strong>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. Navigation Tabs (Classes vs Users) --}}
    <div class="mob-tabs-bar">
        <a class="mob-tab-btn {{ !$isUserTab ? 'active' : '' }}" href="{{ url('attendance/marking-window?tab=classes') }}">
            <i class="fa fa-graduation-cap"></i> Classes Window
        </a>
        <a class="mob-tab-btn {{ $isUserTab ? 'active' : '' }}" href="{{ url('attendance/marking-window?tab=users') }}">
            <i class="fa fa-users"></i> Users Window
        </a>
    </div>

    {{-- Form for Saving Windows --}}
    <form method="post" action="{{ url('attendance/marking-window') }}" id="mobMarkingWindowForm">
        @csrf
        <input type="hidden" name="form_type" value="{{ $activeTab }}">

        {{-- 3. Search & Bulk Action Toolbar --}}
        <div class="mob-tool-card">
            {{-- Instant Search --}}
            <div class="mob-search-input-wrap">
                <i class="fa fa-search"></i>
                <input type="text" id="mob-search-input" class="mob-search-input" placeholder="Search {{ $isUserTab ? 'staff name or role' : 'class name' }}...">
            </div>

            {{-- Bulk Time Set & Shortcuts --}}
            <div class="mob-bulk-row">
                <div class="mob-bulk-times">
                    <span style="font-size: 9.5px; font-weight: 700; color: #475569;">Bulk:</span>
                    <input type="time" id="mob-bulk-from" class="mob-bulk-time-input" value="08:00" title="From Time">
                    <span style="font-size: 9px; color: #64748b;">to</span>
                    <input type="time" id="mob-bulk-to" class="mob-bulk-time-input" value="14:00" title="To Time">
                    <button type="button" class="btn-bulk-apply" id="btn-mob-apply-bulk">
                        <i class="fa fa-clock-o"></i> Apply All
                    </button>
                </div>
                <div class="mob-bulk-shortcuts">
                    <button type="button" class="btn-quick-toggle btn-toggle-enable" id="btn-mob-enable-all" title="Enable All">
                        <i class="fa fa-check"></i> All On
                    </button>
                    <button type="button" class="btn-quick-toggle btn-toggle-disable" id="btn-mob-disable-all" title="Disable All">
                        <i class="fa fa-ban"></i> All Off
                    </button>
                </div>
            </div>
        </div>

        {{-- 4. Marking Window Cards List --}}
        <div class="mob-card-list" id="mobCardList">
            @if(!$isUserTab)
                {{-- Classes List --}}
                @forelse($classes as $index => $class)
                    @php
                        $window = $windows->get($class->id);
                        $key = (string) $class->id;
                        $old = old('windows', []);
                        $fromVal = data_get($old, $key . '.from_time', $timeValue($window->from_time ?? ''));
                        $toVal = data_get($old, $key . '.to_time', $timeValue($window->to_time ?? ''));
                        $isActive = (int) data_get($old, $key . '.is_active', $window->is_active ?? 1) === 1;
                        $notesVal = data_get($old, $key . '.notes', $window->notes ?? '');
                    @endphp
                    <div class="mob-win-card window-card" data-name="{{ strtolower($class->name) }}">
                        <div class="mob-win-card-header">
                            <span class="mob-win-card-title">
                                <i class="fa fa-graduation-cap text-primary"></i> {{ $class->name }}
                            </span>
                            <input type="hidden" name="windows[{{ $key }}][class_type_id]" value="{{ $class->id }}">

                            {{-- Status Toggle --}}
                            <label class="win-switch-wrap">
                                <input type="hidden" name="windows[{{ $key }}][is_active]" value="0">
                                <input type="checkbox" class="row-status-checkbox" name="windows[{{ $key }}][is_active]" value="1" {{ $isActive ? 'checked' : '' }}>
                                <span class="win-switch-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                    <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i>
                                    <span>{{ $isActive ? 'Active' : 'Disabled' }}</span>
                                </span>
                            </label>
                        </div>

                        <div class="mob-win-card-body">
                            {{-- Time Pickers & Duration --}}
                            <div class="mob-time-inputs-grid">
                                <div class="mob-time-group">
                                    <label class="mob-time-lbl"><i class="fa fa-sign-in text-success"></i> From</label>
                                    <input type="time" name="windows[{{ $key }}][from_time]" class="mob-time-input from-time-input" value="{{ $fromVal }}">
                                </div>
                                <div class="mob-time-group">
                                    <label class="mob-time-lbl"><i class="fa fa-sign-out text-danger"></i> To</label>
                                    <input type="time" name="windows[{{ $key }}][to_time]" class="mob-time-input to-time-input" value="{{ $toVal }}">
                                </div>
                                <div class="mob-duration-box">
                                    <span class="mob-duration-pill duration-pill">&ndash;</span>
                                </div>
                            </div>

                            {{-- Notes / Remarks & Reset Button --}}
                            <div class="mob-note-row">
                                <input type="text" name="windows[{{ $key }}][notes]" class="mob-note-input cell-note-input" placeholder="Optional notes / remark..." value="{{ $notesVal }}">
                                <button type="button" class="btn-card-reset btn-clear-card" title="Reset this timing">
                                    <i class="fa fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted" style="font-size: 12px;">No classes found in this session.</div>
                @endforelse
            @else
                {{-- Users / Staff List --}}
                @forelse($users as $index => $user)
                    @php
                        $window = $userWindows->get($user->id);
                        $key = (string) $user->id;
                        $old = old('user_windows', []);
                        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->userName ?? 'User #' . $user->id);
                        $roleName = optional($user->roleName)->name ?? 'Staff';
                        $fromVal = data_get($old, $key . '.from_time', $timeValue($window->from_time ?? ''));
                        $toVal = data_get($old, $key . '.to_time', $timeValue($window->to_time ?? ''));
                        $isActive = (int) data_get($old, $key . '.is_active', $window->is_active ?? 1) === 1;
                        $notesVal = data_get($old, $key . '.notes', $window->notes ?? '');
                    @endphp
                    <div class="mob-win-card window-card" data-name="{{ strtolower($userName . ' ' . $roleName) }}">
                        <div class="mob-win-card-header">
                            <span class="mob-win-card-title">
                                <i class="fa fa-user-circle text-info"></i> {{ $userName }}
                                <span class="mob-role-pill">{{ $roleName }}</span>
                            </span>
                            <input type="hidden" name="user_windows[{{ $key }}][user_id]" value="{{ $user->id }}">

                            {{-- Status Toggle --}}
                            <label class="win-switch-wrap">
                                <input type="hidden" name="user_windows[{{ $key }}][is_active]" value="0">
                                <input type="checkbox" class="row-status-checkbox" name="user_windows[{{ $key }}][is_active]" value="1" {{ $isActive ? 'checked' : '' }}>
                                <span class="win-switch-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                    <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i>
                                    <span>{{ $isActive ? 'Active' : 'Disabled' }}</span>
                                </span>
                            </label>
                        </div>

                        <div class="mob-win-card-body">
                            {{-- Time Pickers & Duration --}}
                            <div class="mob-time-inputs-grid">
                                <div class="mob-time-group">
                                    <label class="mob-time-lbl"><i class="fa fa-sign-in text-success"></i> From</label>
                                    <input type="time" name="user_windows[{{ $key }}][from_time]" class="mob-time-input from-time-input" value="{{ $fromVal }}">
                                </div>
                                <div class="mob-time-group">
                                    <label class="mob-time-lbl"><i class="fa fa-sign-out text-danger"></i> To</label>
                                    <input type="time" name="user_windows[{{ $key }}][to_time]" class="mob-time-input to-time-input" value="{{ $toVal }}">
                                </div>
                                <div class="mob-duration-box">
                                    <span class="mob-duration-pill duration-pill">&ndash;</span>
                                </div>
                            </div>

                            {{-- Notes / Remarks & Reset Button --}}
                            <div class="mob-note-row">
                                <input type="text" name="user_windows[{{ $key }}][notes]" class="mob-note-input cell-note-input" placeholder="Optional notes / remark..." value="{{ $notesVal }}">
                                <button type="button" class="btn-card-reset btn-clear-card" title="Reset this timing">
                                    <i class="fa fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted" style="font-size: 12px;">No staff users found in this session.</div>
                @endforelse
            @endif
        </div>

        {{-- 5. Fixed Bottom Save Toolbar --}}
        <div class="mob-save-toolbar">
            <div class="mob-save-toolbar-info">
                <span>Total: <b>{{ $activeItemsCount }}</b></span> &bull;
                <span>Active: <b id="bottom-active-count" class="text-success">{{ $isUserTab ? $userActiveCount : $classActiveCount }}</b></span>
                <span id="mod-wrap" style="display: none;"> &bull; <b id="bottom-modified-count" class="text-warning">0</b> changed</span>
            </div>
            <button type="submit" class="btn-mob-save">
                <i class="fa fa-save"></i> Save Windows
            </button>
        </div>

    </form>
</div>

{{-- Confirmation Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="mobConfirmBackdrop"></div>
<div class="mob-bottom-sheet" id="mobConfirmSheet">
    <div class="mob-sheet-handle"></div>
    <div class="mob-sheet-header">
        <div class="mob-sheet-icon is-primary" id="mobConfirmIcon">
            <i class="fa fa-clock-o"></i>
        </div>
        <h3 class="mob-sheet-title" id="mobConfirmTitle">Confirm Action</h3>
    </div>
    <div class="mob-sheet-body" id="mobConfirmMessage">
        Are you sure you want to apply this action?
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-btn mob-sheet-btn-cancel" id="mobConfirmBtnCancel">Cancel</button>
        <button type="button" class="mob-sheet-btn mob-sheet-btn-confirm" id="mobConfirmBtnOk">Proceed</button>
    </div>
</div>

{{-- Toast Notification --}}
<div id="mobToast">
    <i class="fa fa-check-circle text-success"></i>
    <span id="mobToastMsg">Notification message</span>
</div>

<script>
$(document).ready(function() {
    let modifiedCount = 0;

    function showToast(msg, isSuccess = true) {
        const $t = $('#mobToast');
        $('#mobToastMsg').text(msg);
        $t.find('i').removeClass('fa-check-circle text-success fa-exclamation-triangle text-danger')
            .addClass(isSuccess ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-danger');
        $t.addClass('show');
        setTimeout(() => $t.removeClass('show'), 2400);
    }

    let confirmCallback = null;
    function openConfirmSheet(title, msg, onConfirm, iconClass = 'fa-clock-o', isDanger = false) {
        $('#mobConfirmTitle').text(title);
        $('#mobConfirmMessage').html(msg);
        $('#mobConfirmIcon i').removeClass().addClass('fa ' + iconClass);
        $('#mobConfirmIcon').removeClass('is-primary is-danger').addClass(isDanger ? 'is-danger' : 'is-primary');
        $('#mobConfirmBtnOk').removeClass('btn-danger').css('background', isDanger ? '#dc2626' : '#002C54');
        confirmCallback = onConfirm;
        $('#mobConfirmBackdrop').addClass('show');
        $('#mobConfirmSheet').addClass('show');
    }

    function closeConfirmSheet() {
        $('#mobConfirmBackdrop').removeClass('show');
        $('#mobConfirmSheet').removeClass('show');
        confirmCallback = null;
    }

    $('#mobConfirmBtnCancel, #mobConfirmBackdrop').on('click', closeConfirmSheet);
    $('#mobConfirmBtnOk').on('click', function() {
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        closeConfirmSheet();
    });

    function markModified() {
        modifiedCount++;
        $('#bottom-modified-count').text(modifiedCount);
        $('#mod-wrap').show();
    }

    function calculateDuration(from, to) {
        if (!from || !to) return '&ndash;';
        const fromParts = from.split(':');
        const toParts = to.split(':');
        if (fromParts.length < 2 || toParts.length < 2) return '&ndash;';

        const fromMin = parseInt(fromParts[0], 10) * 60 + parseInt(fromParts[1], 10);
        let toMin = parseInt(toParts[0], 10) * 60 + parseInt(toParts[1], 10);

        if (toMin < fromMin) toMin += 24 * 60; // Crosses midnight

        const diffMin = toMin - fromMin;
        const hrs = Math.floor(diffMin / 60);
        const mins = diffMin % 60;

        if (hrs === 0 && mins === 0) return '0 min';
        return (hrs > 0 ? hrs + 'h ' : '') + (mins > 0 ? mins + 'm' : '').trim();
    }

    function updateCardDuration($card) {
        const from = $card.find('.from-time-input').val();
        const to = $card.find('.to-time-input').val();
        $card.find('.duration-pill').html(calculateDuration(from, to));
    }

    function updateActiveCount() {
        const activeCount = $('.row-status-checkbox:checked').length;
        $('#bottom-active-count').text(activeCount);
        $('#hero-active-count').text(activeCount);
    }

    // Initialize all card durations
    $('.window-card').each(function() {
        updateCardDuration($(this));
    });

    // Time input changes
    $('.from-time-input, .to-time-input').on('change input', function() {
        const $card = $(this).closest('.window-card');
        updateCardDuration($card);
        markModified();
    });

    // Notes changes
    $('.cell-note-input').on('input', function() {
        markModified();
    });

    // Switch checkbox change
    $('.row-status-checkbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        const $pill = $(this).siblings('.win-switch-pill');

        if (isChecked) {
            $pill.removeClass('is-inactive').addClass('is-active');
            $pill.find('i').removeClass('fa-ban').addClass('fa-check');
            $pill.find('span').text('Active');
        } else {
            $pill.removeClass('is-active').addClass('is-inactive');
            $pill.find('i').removeClass('fa-check').addClass('fa-ban');
            $pill.find('span').text('Disabled');
        }
        updateActiveCount();
        markModified();
    });

    // Bulk Set Timings with confirmation sheet
    $('#btn-mob-apply-bulk').on('click', function() {
        const from = $('#mob-bulk-from').val();
        const to = $('#mob-bulk-to').val();

        if (!from || !to) {
            showToast('Please choose both From and To time.', false);
            return;
        }

        const visibleCount = $('.window-card:visible').length;
        openConfirmSheet(
            'Apply Bulk Timings',
            `Set timings <b>${from} to ${to}</b> for all <b>${visibleCount}</b> visible window cards?`,
            function() {
                $('.window-card:visible').each(function() {
                    $(this).find('.from-time-input').val(from);
                    $(this).find('.to-time-input').val(to);
                    updateCardDuration($(this));
                });
                markModified();
                showToast(`Applied ${from} - ${to} to ${visibleCount} cards.`);
            },
            'fa-clock-o',
            false
        );
    });

    // Enable All with confirmation
    $('#btn-mob-enable-all').on('click', function() {
        const visibleCount = $('.window-card:visible').length;
        openConfirmSheet(
            'Enable All Windows',
            `Enable marking status for all <b>${visibleCount}</b> visible window cards?`,
            function() {
                $('.window-card:visible').each(function() {
                    const $chk = $(this).find('.row-status-checkbox');
                    if (!$chk.is(':checked')) {
                        $chk.prop('checked', true).trigger('change');
                    }
                });
                showToast(`Enabled all ${visibleCount} cards.`);
            },
            'fa-check-circle',
            false
        );
    });

    // Disable All with confirmation
    $('#btn-mob-disable-all').on('click', function() {
        const visibleCount = $('.window-card:visible').length;
        openConfirmSheet(
            'Disable All Windows',
            `Disable marking status for all <b>${visibleCount}</b> visible window cards?`,
            function() {
                $('.window-card:visible').each(function() {
                    const $chk = $(this).find('.row-status-checkbox');
                    if ($chk.is(':checked')) {
                        $chk.prop('checked', false).trigger('change');
                    }
                });
                showToast(`Disabled all ${visibleCount} cards.`);
            },
            'fa-ban',
            true
        );
    });

    // Clear single card
    $('.btn-clear-card').on('click', function() {
        const $card = $(this).closest('.window-card');
        $card.find('.from-time-input').val('');
        $card.find('.to-time-input').val('');
        $card.find('.cell-note-input').val('');
        updateCardDuration($card);
        markModified();
        showToast('Card timing cleared.');
    });

    // Real-time Instant Search
    $('#mob-search-input').on('input', function() {
        const term = ($(this).val() || '').trim().toLowerCase();
        let visibleCount = 0;

        $('.window-card').each(function() {
            const name = $(this).data('name') || '';
            if (!term || name.indexOf(term) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endsection