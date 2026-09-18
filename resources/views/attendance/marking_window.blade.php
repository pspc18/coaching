@extends('layout.app')

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
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ATTENDANCE MARKING WINDOW (NEW THEME COMPACT GUIDELINES)
   - Viewport fitting: calc(100vh - var(--header-height, 56px) - 16px)
   - Zero outer page vertical scrollbars
   - Dark Navy Hero (#002C54 to #0f3460)
   - Bulk Time setter & 1-click status shortcuts
   - Sticky header table with internal smooth scrolling
   - Pinned bottom save toolbar
   ========================================================================== */

.att-win-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.att-win-page * {
    box-sizing: border-box;
}
.att-win-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    gap: 5px;
}

/* 1. Top Hero Header (Arise Signature Navy Theme) */
.att-win-hero {
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
.att-win-hero-text {
    display: flex;
    flex-direction: column;
}
.att-win-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    font-weight: 600;
    color: #93c5fd;
}
.att-win-title {
    font-size: 15px;
    font-weight: 700;
    margin: 1px 0 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-win-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Right Summary Badges */
.att-win-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.win-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.win-stat-badge b {
    font-weight: 700;
}
.win-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.win-stat-badge.badge-blue {
    background: rgba(59, 130, 246, 0.22);
    border-color: rgba(59, 130, 246, 0.4);
    color: #bfdbfe;
}

/* 2. Navigation Tabs (Classes vs Users) */
.att-win-nav-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #002C54;
    padding: 3px 6px;
    border-radius: 2px;
    flex-shrink: 0;
    border-bottom: 2px solid #001f3d;
}
.att-win-tab-link {
    padding: 5px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: #cbd5e1;
    border-radius: 2px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: 1px solid transparent;
    transition: all .15s ease-in-out;
}
.att-win-tab-link:hover {
    color: #ffffff;
    background: rgba(255,255,255,.1);
}
.att-win-tab-link.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #38bdf8;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.att-win-tab-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    background: rgba(0,0,0,.25);
    color: #ffffff;
}

/* 3. Action Bar & Bulk Controls */
.att-win-action-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.att-win-search-box {
    position: relative;
    width: 220px;
}
.att-win-search-box i {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.att-win-search-input {
    width: 100%;
    height: 27px;
    padding-left: 26px;
    padding-right: 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: border-color .15s;
}
.att-win-search-input:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0,44,84,.1);
}

.att-win-bulk-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.bulk-time-label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
}
.time-input-compact {
    height: 27px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 6px;
    color: #0f172a;
    outline: none;
    background: #ffffff;
}
.time-input-compact:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0,44,84,.1);
}
.btn-bulk-apply {
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    background: #0284c7;
    border: 1px solid #0284c7;
    color: #ffffff;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s;
}
.btn-bulk-apply:hover {
    background: #0369a1;
}
.btn-quick-toggle {
    height: 27px;
    padding: 0 9px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s;
}
.btn-toggle-enable {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #047857;
}
.btn-toggle-enable:hover {
    background: #d1fae5;
}
.btn-toggle-disable {
    background: #fef2f2;
    border-color: #fecaca;
    color: #b91c1c;
}
.btn-toggle-disable:hover {
    background: #fee2e2;
}

/* Save Button */
.btn-att-save {
    height: 27px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 700;
    background: #002C54;
    border: 1px solid #002C54;
    color: #ffffff;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all .15s;
}
.btn-att-save:hover {
    background: #001930;
    border-color: #001930;
}

/* 4. Main Table Card & Grid */
#marking-window-form {
    flex: 1 1 0%;
    min-height: 0 !important;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin: 0;
}
.att-win-table-card {
    flex: 1 1 0%;
    min-height: 0 !important;
    display: flex;
    flex-direction: column;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    overflow: hidden;
}
.att-win-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.att-win-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-win-counter-badge {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* Scroll Container */
.att-win-scroll-container {
    flex: 1 1 0%;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Grid Table */
.att-win-grid-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.att-win-grid-table thead th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 6px 8px;
    height: 34px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 10;
    vertical-align: middle;
}
.att-win-grid-table tbody td {
    padding: 4px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    background: #ffffff;
}
.att-win-grid-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.att-win-grid-table tbody tr:nth-child(even) td {
    background: #ffffff;
}
.att-win-grid-table tbody tr:hover td {
    background: #e2e8f0 !important;
}

/* Table Controls */
.cell-name-box {
    font-weight: 700;
    color: #0f172a;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.role-badge {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 6px;
    border-radius: 2px;
    background: #e2e8f0;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.cell-time-input {
    width: 125px;
    height: 26px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 6px;
    color: #0f172a;
    outline: none;
    text-align: center;
    transition: all .15s;
    background: #ffffff;
}
.cell-time-input:focus {
    border-color: #0284c7;
    background: #f0f9ff;
    box-shadow: 0 0 0 1px #0284c7;
}
.cell-note-input {
    width: 100%;
    height: 26px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 8px;
    color: #0f172a;
    outline: none;
    background: #ffffff;
    transition: border-color .15s;
}
.cell-note-input:focus {
    border-color: #002C54;
    background: #f8fafc;
}

/* Custom Status Switch */
.win-switch-wrap {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    user-select: none;
    margin: 0;
}
.win-switch-wrap input {
    display: none;
}
.win-switch-pill {
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
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

/* Duration Pill */
.duration-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    white-space: nowrap;
}

/* Quick Action Row Buttons */
.btn-row-action {
    width: 24px;
    height: 24px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #64748b;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    transition: all .15s;
}
.btn-row-action:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 5. Pinned Bottom Summary Toolbar */
.att-win-bottom-bar {
    background: #002C54;
    color: #ffffff;
    height: 38px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.att-win-bottom-info {
    font-size: 11px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 12px;
}
.att-win-bottom-info b {
    color: #ffffff;
}
.att-win-bottom-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper att-win-page p-2">
    <div class="container-fluid p-0">
        <div class="att-win-layout">

            {{-- 1. Top Hero Header (Arise Signature Navy Theme) --}}
            <div class="att-win-hero">
                <div class="att-win-hero-text">
                    <span class="att-win-kicker"><i class="fa fa-clock-o mr-1"></i> Attendance Management</span>
                    <h1 class="att-win-title">
                        Attendance Marking Windows
                    </h1>
                    <p class="att-win-subtitle">Configure allowed start &amp; end timing windows for daily student and staff attendance marking</p>
                </div>

                {{-- Hero Right Badges --}}
                <div class="att-win-hero-stats">
                    <span class="win-stat-badge badge-blue" title="Total Configured Windows">
                        <i class="fa fa-calendar-check-o"></i> Configured: <b>{{ $classConfigured + $userConfigured }}</b>
                    </span>
                    <span class="win-stat-badge badge-green" title="Active Windows">
                        <i class="fa fa-toggle-on"></i> Active: <b>{{ $classActiveCount + $userActiveCount }}</b>
                    </span>
                    <span class="win-stat-badge" title="Active Mode">
                        <i class="fa {{ $activeTab === 'classes' ? 'fa-graduation-cap' : 'fa-users' }}"></i> {{ ucfirst($activeTab) }} Mode
                    </span>
                </div>
            </div>

            {{-- Alerts & Notifications --}}
            @if(session('message'))
                <div class="alert alert-success py-2 mb-1" style="font-size: 11.5px; border-radius: 2px;">
                    <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                </div>
            @endif
            @if(!empty($errors) && $errors->any())
                <div class="alert alert-danger py-2 mb-1" style="font-size: 11.5px; border-radius: 2px;">
                    <i class="fa fa-exclamation-circle mr-1"></i>
                    <strong>Please resolve the following errors:</strong>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 2. Navigation Tabs (Classes vs Users) --}}
            <div class="att-win-nav-tabs">
                <a class="att-win-tab-link {{ $activeTab === 'classes' ? 'active' : '' }}" href="{{ url('attendance/marking-window?tab=classes') }}">
                    <i class="fa fa-graduation-cap"></i> Classes Marking Window
                    <span class="att-win-tab-badge">{{ $classConfigured }} Configured</span>
                </a>
                <a class="att-win-tab-link {{ $activeTab === 'users' ? 'active' : '' }}" href="{{ url('attendance/marking-window?tab=users') }}">
                    <i class="fa fa-users"></i> Users Marking Window
                    <span class="att-win-tab-badge">{{ $userConfigured }} Configured</span>
                </a>
            </div>

            {{-- 3. Form & Controls Area --}}
            <form method="post" action="{{ url('attendance/marking-window') }}" id="marking-window-form">
                @csrf
                <input type="hidden" name="form_type" value="{{ $activeTab }}">

                {{-- Bulk Action & Quick Filter Toolbar --}}
                <div class="att-win-action-bar">
                    {{-- Search Input --}}
                    <div class="att-win-search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="window-instant-search" class="att-win-search-input" placeholder="Search {{ $activeTab === 'classes' ? 'class' : 'user or role' }}...">
                    </div>

                    {{-- Bulk Time Setter --}}
                    <div class="att-win-bulk-group">
                        <span class="bulk-time-label"><i class="fa fa-bolt text-warning"></i> Bulk Set:</span>
                        <input type="time" id="bulk-from-time" class="time-input-compact" value="08:00" title="From Time">
                        <span class="text-muted" style="font-size: 11px;">to</span>
                        <input type="time" id="bulk-to-time" class="time-input-compact" value="14:00" title="To Time">
                        <button type="button" class="btn-bulk-apply" id="btn-apply-bulk-time" title="Apply these timings to all visible rows">
                            <i class="fa fa-clock-o"></i> Set for All
                        </button>

                        <div class="d-none d-md-inline-flex align-items-center" style="gap: 4px;">
                            <button type="button" class="btn-quick-toggle btn-toggle-enable" id="btn-enable-all" title="Enable all windows">
                                <i class="fa fa-check"></i> Enable All
                            </button>
                            <button type="button" class="btn-quick-toggle btn-toggle-disable" id="btn-disable-all" title="Disable all windows">
                                <i class="fa fa-ban"></i> Disable All
                            </button>
                        </div>
                    </div>

                    {{-- Top Save Button --}}
                    <button type="submit" class="btn-att-save">
                        <i class="fa fa-save"></i> Save Windows
                    </button>
                </div>

                {{-- 4. Full Viewport Table Card --}}
                <div class="att-win-table-card">
                    <div class="att-win-card-header">
                        <h3 class="att-win-card-title">
                            <i class="fa fa-table text-info"></i>
                            {{ $activeTab === 'classes' ? 'Class Wise Attendance Windows' : 'User Wise Attendance Windows' }}
                        </h3>
                        <div>
                            <span class="att-win-counter-badge">
                                <span id="badge-visible-count">{{ $activeItemsCount }}</span> / {{ $activeItemsCount }} Records
                            </span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="att-win-scroll-container">
                        <table class="att-win-grid-table" id="markingWindowGridTable">
                            <thead>
                                <tr>
                                    <th style="width: 44px;" class="text-center">#</th>
                                    @if($activeTab === 'classes')
                                        <th style="min-width: 180px;">Class Name</th>
                                    @else
                                        <th style="min-width: 180px;">User Name</th>
                                        <th style="width: 130px;" class="text-center">Role</th>
                                    @endif
                                    <th style="width: 145px;" class="text-center">From Time</th>
                                    <th style="width: 145px;" class="text-center">To Time</th>
                                    <th style="width: 110px;" class="text-center">Duration</th>
                                    <th style="width: 110px;" class="text-center">Status</th>
                                    <th style="min-width: 200px;">Notes / Remarks</th>
                                    <th style="width: 70px;" class="text-center">Reset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($activeTab === 'classes')
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
                                        <tr class="window-row" data-name="{{ strtolower($class->name) }}">
                                            <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="cell-name-box">
                                                    <i class="fa fa-graduation-cap text-primary"></i>
                                                    <span>{{ $class->name }}</span>
                                                </div>
                                                <input type="hidden" name="windows[{{ $key }}][class_type_id]" value="{{ $class->id }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="time" name="windows[{{ $key }}][from_time]" class="cell-time-input from-time-input" value="{{ $fromVal }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="time" name="windows[{{ $key }}][to_time]" class="cell-time-input to-time-input" value="{{ $toVal }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="duration-pill">&ndash;</span>
                                            </td>
                                            <td class="text-center">
                                                <label class="win-switch-wrap">
                                                    <input type="hidden" name="windows[{{ $key }}][is_active]" value="0">
                                                    <input type="checkbox" class="row-status-checkbox" name="windows[{{ $key }}][is_active]" value="1" {{ $isActive ? 'checked' : '' }}>
                                                    <span class="win-switch-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                                        <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i>
                                                        <span>{{ $isActive ? 'Active' : 'Disabled' }}</span>
                                                    </span>
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="windows[{{ $key }}][notes]" class="cell-note-input" placeholder="Optional remark/note..." value="{{ $notesVal }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn-row-action btn-clear-row" title="Clear this row's timing">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No classes found in this session.</td>
                                        </tr>
                                    @endforelse
                                @else
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
                                        <tr class="window-row" data-name="{{ strtolower($userName . ' ' . $roleName) }}">
                                            <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="cell-name-box">
                                                    <i class="fa fa-user-circle text-info"></i>
                                                    <span>{{ $userName }}</span>
                                                </div>
                                                <input type="hidden" name="user_windows[{{ $key }}][user_id]" value="{{ $user->id }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="role-badge">{{ $roleName }}</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="time" name="user_windows[{{ $key }}][from_time]" class="cell-time-input from-time-input" value="{{ $fromVal }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="time" name="user_windows[{{ $key }}][to_time]" class="cell-time-input to-time-input" value="{{ $toVal }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="duration-pill">&ndash;</span>
                                            </td>
                                            <td class="text-center">
                                                <label class="win-switch-wrap">
                                                    <input type="hidden" name="user_windows[{{ $key }}][is_active]" value="0">
                                                    <input type="checkbox" class="row-status-checkbox" name="user_windows[{{ $key }}][is_active]" value="1" {{ $isActive ? 'checked' : '' }}>
                                                    <span class="win-switch-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                                        <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i>
                                                        <span>{{ $isActive ? 'Active' : 'Disabled' }}</span>
                                                    </span>
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="user_windows[{{ $key }}][notes]" class="cell-note-input" placeholder="Optional remark/note..." value="{{ $notesVal }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn-row-action btn-clear-row" title="Clear this row's timing">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No staff members found in this session.</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- 5. Pinned Bottom Summary Toolbar --}}
                    <div class="att-win-bottom-bar">
                        <div class="att-win-bottom-info">
                            <span>Total Rows: <b>{{ $activeItemsCount }}</b></span>
                            <span>Active Windows: <b id="bottom-active-count" class="text-success">{{ $activeTab === 'classes' ? $classActiveCount : $userActiveCount }}</b></span>
                            <span>Modified: <b id="bottom-modified-count" class="text-warning">0</b></span>
                        </div>
                        <div class="att-win-bottom-actions">
                            <button type="submit" class="btn-att-save">
                                <i class="fa fa-save"></i> Save {{ $activeTab === 'classes' ? 'Class' : 'User' }} Windows
                            </button>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let modifiedCount = 0;

    function markModified() {
        modifiedCount++;
        $('#bottom-modified-count').text(modifiedCount);
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

    function updateRowDuration($row) {
        const from = $row.find('.from-time-input').val();
        const to = $row.find('.to-time-input').val();
        $row.find('.duration-pill').html(calculateDuration(from, to));
    }

    function updateActiveCount() {
        const activeCount = $('.row-status-checkbox:checked').length;
        $('#bottom-active-count').text(activeCount);
    }

    // Initialize all row durations
    $('.window-row').each(function() {
        updateRowDuration($(this));
    });

    // Time input changes
    $('.from-time-input, .to-time-input').on('change input', function() {
        const $row = $(this).closest('.window-row');
        updateRowDuration($row);
        markModified();
    });

    // Note input changes
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

    // Bulk Set Timings
    $('#btn-apply-bulk-time').on('click', function() {
        const from = $('#bulk-from-time').val();
        const to = $('#bulk-to-time').val();

        if (!from || !to) {
            alert('Please specify both From and To time.');
            return;
        }

        let updated = 0;
        $('.window-row:visible').each(function() {
            $(this).find('.from-time-input').val(from);
            $(this).find('.to-time-input').val(to);
            updateRowDuration($(this));
            updated++;
        });

        markModified();
    });

    // Enable All
    $('#btn-enable-all').on('click', function() {
        $('.window-row:visible').each(function() {
            const $chk = $(this).find('.row-status-checkbox');
            if (!$chk.is(':checked')) {
                $chk.prop('checked', true).trigger('change');
            }
        });
    });

    // Disable All
    $('#btn-disable-all').on('click', function() {
        $('.window-row:visible').each(function() {
            const $chk = $(this).find('.row-status-checkbox');
            if ($chk.is(':checked')) {
                $chk.prop('checked', false).trigger('change');
            }
        });
    });

    // Clear single row
    $('.btn-clear-row').on('click', function() {
        const $row = $(this).closest('.window-row');
        $row.find('.from-time-input').val('');
        $row.find('.to-time-input').val('');
        $row.find('.cell-note-input').val('');
        updateRowDuration($row);
        markModified();
    });

    // Real-time Instant Search
    $('#window-instant-search').on('input', function() {
        const term = ($(this).val() || '').trim().toLowerCase();
        let visibleCount = 0;

        $('.window-row').each(function() {
            const name = $(this).data('name') || '';
            if (!term || name.indexOf(term) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#badge-visible-count').text(visibleCount);
    });
});
</script>
@endsection
