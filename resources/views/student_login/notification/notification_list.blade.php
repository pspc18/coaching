@php
    $getUser = Helper::getUser();
    $category = strtolower(trim((string) ($category ?? 'all')));
    $allowedCategories = ['all', 'attendance', 'fees', 'result', 'notice', 'complaint'];
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'all';
    }

    $pageLabel = 'Notifications';
    $selectedCategoryLabel = [
        'all' => 'All Modules',
        'attendance' => 'Attendance',
        'fees' => 'Fees',
        'result' => 'Exam Results',
        'notice' => 'Notices',
        'complaint' => 'Complaints',
    ][$category] ?? 'All Modules';

    $categoryMeta = [
        'all' => ['label' => 'All', 'icon' => 'fa-bell-o', 'color' => '#0284c7'],
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-calendar-check-o', 'color' => '#0284c7'],
        'fees' => ['label' => 'Fees', 'icon' => 'fa-inr', 'color' => '#10b981'],
        'result' => ['label' => 'Results', 'icon' => 'fa-file-text-o', 'color' => '#8b5cf6'],
        'notice' => ['label' => 'Notices', 'icon' => 'fa-bullhorn', 'color' => '#f59e0b'],
        'complaint' => ['label' => 'Complaints', 'icon' => 'fa-comments-o', 'color' => '#64748b'],
    ];

    // If a category is selected, bring it to the front (left starting position)
    if ($category !== 'all' && isset($categoryMeta[$category])) {
        $selectedCat = [$category => $categoryMeta[$category]];
        unset($categoryMeta[$category]);
        $categoryMeta = $selectedCat + $categoryMeta;
    }

    $buildUrl = function (array $params) {
        $params = array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
        return url('notificationFatchStudent' . (count($params) ? '?' . http_build_query($params) : ''));
    };

    $currentSearch = (string) ($search ?? request('search', ''));
    $studentName = Session::get('first_name') ?: ($getUser->first_name ?? 'Student');
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
    $totalCount = $notifications->total();
@endphp
@extends('student_login.layout.app')

@section('title', 'Notifications & Alerts')
@section('page_title', 'NOTIFICATIONS')
@section('page_sub', $studentName . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE NOTIFICATIONS & ALERTS
   Exact Layout, Typography & Cohesive Theme System
   ========================================================================== */

.mob-notif-page {
    padding: 0 0 calc(var(--bottom-nav-height, 52px) + 54px + var(--safe-bottom, 0px));
    color: #1e293b;
    width: 100%;
    box-sizing: border-box;
}

/* 1. Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    border-radius: 4px;
    padding: 12px 14px 10px;
    color: #ffffff;
    box-shadow: 0 6px 20px -2px rgba(0, 24, 51, 0.28);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}

.mob-hero-card::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(56, 189, 248, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
}

.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.2px;
    margin: 0;
    line-height: 1.25;
    display: flex;
    align-items: center;
    gap: 6px;
}

.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.16);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

/* 4-Metric Radar Grid (2x2) */
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    position: relative;
    z-index: 1;
}

.mob-radar-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px 8px;
}

.mob-radar-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.mob-radar-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 2px 0 1px;
}

.mob-radar-val.danger-badge {
    color: #f87171;
}

.mob-radar-val.success-badge {
    color: #4ade80;
}

.mob-radar-meta {
    font-size: 8.5px;
    color: #cbd5e1;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Action Strip */
.mob-hero-actions {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    position: relative;
    z-index: 1;
}

.mob-btn-hero {
    font-size: 9.5px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.3;
}

.mob-btn-hero.btn-mark-all {
    background: rgba(56, 189, 248, 0.18);
    border-color: rgba(56, 189, 248, 0.4);
    color: #7dd3fc;
}

.mob-btn-hero.btn-mark-all:hover {
    background: rgba(56, 189, 248, 0.3);
    color: #ffffff;
}

.mob-btn-hero.btn-clear-all {
    background: rgba(239, 68, 68, 0.16);
    border-color: rgba(239, 68, 68, 0.35);
    color: #fca5a5;
}

.mob-btn-hero.btn-clear-all:hover {
    background: rgba(239, 68, 68, 0.28);
    color: #ffffff;
}

/* 2. Compact Search & Filter Toolbar */
.mob-filter-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 7px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.mob-search-row {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
}

.mob-search-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.mob-search-wrap i.search-icon {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}

.mob-search-input {
    width: 100%;
    height: 30px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 24px 0 26px;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.mob-search-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.mob-search-clear {
    position: absolute;
    right: 6px;
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 11px;
    cursor: pointer;
    padding: 0;
    display: none;
    align-items: center;
    justify-content: center;
}

.mob-search-submit {
    height: 30px;
    padding: 0 10px;
    background: #002C54;
    border: 1px solid #002C54;
    color: #ffffff;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    flex-shrink: 0;
}

/* Horizontal Scrollable Category Pills */
.mob-pill-scroll {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    padding-bottom: 3px;
    margin-bottom: 6px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.mob-pill-scroll::-webkit-scrollbar {
    display: none;
}

.mob-pill-btn {
    white-space: nowrap;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569 !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.mob-pill-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.mob-pill-btn.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff !important;
}

.mob-pill-badge {
    background: rgba(0, 0, 0, 0.08);
    color: inherit;
    font-size: 8.5px;
    padding: 0 4px;
    border-radius: 9px;
    min-width: 14px;
    text-align: center;
    font-weight: 800;
}

.mob-pill-btn.active .mob-pill-badge {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* Read / Unread Status Segmented Tabs */
.mob-status-tabs {
    display: flex;
    background: #f1f5f9;
    border-radius: 3px;
    padding: 2px;
    gap: 2px;
}

.mob-tab-btn {
    flex: 1;
    text-align: center;
    padding: 4px 6px;
    font-size: 10px;
    font-weight: 700;
    color: #64748b !important;
    text-decoration: none !important;
    border-radius: 2px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.mob-tab-btn.active {
    background: #ffffff;
    color: #002C54 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
}

.mob-tab-count {
    background: #f87171;
    color: #ffffff;
    font-size: 8px;
    padding: 0 4px;
    border-radius: 8px;
    font-weight: 800;
}

/* 3. Notification Card Stream */
.mob-notif-stream {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mob-notif-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #94a3b8;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.mob-notif-card.is-unread {
    background: #fcfdff;
    border-left-color: #0284c7;
    border-top: 1px solid #bfdbfe;
    border-right: 1px solid #bfdbfe;
    border-bottom: 1px solid #bfdbfe;
}

.mob-notif-card.tone-blue   { border-left-color: #0284c7; }
.mob-notif-card.tone-green  { border-left-color: #10b981; }
.mob-notif-card.tone-purple { border-left-color: #8b5cf6; }
.mob-notif-card.tone-amber  { border-left-color: #f59e0b; }
.mob-notif-card.tone-red    { border-left-color: #ef4444; }

.mob-notif-head {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 6px;
}

.mob-notif-icon {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    color: #ffffff;
    background: #0284c7;
}

.mob-notif-icon.tone-blue   { background: #0284c7; }
.mob-notif-icon.tone-green  { background: #10b981; }
.mob-notif-icon.tone-purple { background: #8b5cf6; }
.mob-notif-icon.tone-amber  { background: #f59e0b; }
.mob-notif-icon.tone-red    { background: #ef4444; }

.mob-notif-info {
    flex: 1;
    min-width: 0;
}

.mob-notif-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.mob-notif-title {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
}

.mob-notif-title:hover {
    color: #0284c7;
}

.mob-notif-badge-new {
    font-size: 8px;
    background: #0284c7;
    color: #ffffff;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    flex-shrink: 0;
}

.mob-notif-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 1px;
}

.mob-notif-type-tag {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.mob-notif-time {
    font-size: 9.5px;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.mob-notif-body {
    font-size: 11px;
    line-height: 1.5;
    color: #334155;
    word-break: break-word;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
}

.mob-notif-body.collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.mob-notif-attach {
    display: inline-flex;
    max-width: 100%;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    color: #1d4ed8;
    text-decoration: none !important;
    margin-bottom: 6px;
    box-sizing: border-box;
    overflow: hidden;
    vertical-align: middle;
}

.mob-notif-attach:hover {
    background: #dbeafe;
    color: #1e40af;
}

.mob-notif-attach span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    flex: 1;
}

.mob-notif-attach i {
    flex-shrink: 0;
}

.mob-notif-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 4px;
    border-top: 1px solid #f1f5f9;
    font-size: 9.5px;
    color: #64748b;
}

.mob-notif-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}

.mob-btn-action {
    font-size: 9px;
    font-weight: 700;
    padding: 2.5px 7px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all 0.12s ease;
    line-height: 1.25;
}

.mob-btn-action:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.mob-btn-action.btn-mark-read {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #059669;
}

.mob-btn-action.btn-mark-read:hover {
    background: #d1fae5;
    color: #047857;
}

.mob-btn-action.btn-view-quick {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #0284c7;
}

.mob-btn-action.btn-view-quick:hover {
    background: #dbeafe;
    color: #0369a1;
}

/* Empty State */
.mob-empty-box {
    text-align: center;
    padding: 24px 14px;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 8px 0;
}

.mob-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 8px;
}

.mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 3px;
}

.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    margin: 0 0 10px;
    line-height: 1.45;
}

.mob-btn-reset {
    font-size: 10px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    background: #002C54;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.2);
}

/* ==========================================================================
   4. Fixed Bottom Pagination Bar (Positioned Directly Above Floating Bottom Dock)
   ========================================================================== */
.mob-pagination-fixed-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 10px);
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 16px);
    max-width: 464px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 18px rgba(0, 24, 51, 0.16), 0 1px 3px rgba(0, 0, 0, 0.06);
    box-sizing: border-box;
}

.mob-pagination-info {
    font-size: 11px;
    color: #475569;
    font-weight: 600;
    white-space: nowrap;
}

.mob-pagination-info b {
    color: #002C54;
    font-weight: 800;
}

.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}

.mob-page-btn {
    min-width: 28px;
    height: 28px;
    padding: 0 7px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #002C54;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    line-height: 1;
}

.mob-page-btn:hover:not(.disabled):not(.active) {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #001833;
}

.mob-page-btn.active {
    background: #002C54 !important;
    border-color: #002C54 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.28);
}

.mob-page-btn.disabled {
    opacity: 0.38;
    background: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
    pointer-events: none;
}


/* ==========================================================================
   5. Mobile Custom Bottom Sheet Modal (Matching notices & homework sheets)
   ========================================================================== */
.mob-notif-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: opacity .22s ease-in-out, visibility .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.mob-notif-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}

.mob-notif-sheet {
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 2px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}

.mob-notif-sheet.show {
    bottom: 0;
}

body.mob-sheet-lock {
    overflow: hidden !important;
}

.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 6px 6px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.mob-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}

.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}

.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* Fixed Meta Summary Strip */
.mob-sheet-fixed-meta {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    flex-shrink: 0;
    z-index: 2;
}

.sheet-fixed-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.mob-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2.5px 7px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
}

.badge-soft-info    { background: #e0f2fe; color: #0284c7; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.badge-soft-purple  { background: #ede9fe; color: #6d28d9; }
.badge-soft-amber   { background: #fef3c7; color: #b45309; }
.badge-soft-danger  { background: #fee2e2; color: #b91c1c; }
.badge-soft-slate   { background: #f1f5f9; color: #475569; }

.sheet-meta-pill {
    font-size: 10px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2.5px 8px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.sheet-status-unread {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}

.sheet-status-read {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #15803d;
}

/* Scrollable Body */
.mob-sheet-body {
    padding: 14px 14px 18px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

.sheet-notif-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.35;
    margin: 0 0 10px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    word-break: break-word;
}

.sheet-notif-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 11px 12px;
}

.sheet-notif-text {
    font-size: 12px;
    line-height: 1.65;
    color: #1e293b;
    white-space: pre-line;
    word-break: break-word;
}

/* Attachment Pill */
.sheet-attach-wrap {
    margin-top: 12px;
}

.mob-pdf-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 12px;
    text-decoration: none;
    color: #0f172a;
    transition: all .15s ease;
}

.mob-pdf-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

.mob-pdf-icon {
    font-size: 20px;
    color: #0284c7;
    flex-shrink: 0;
}

.mob-pdf-info {
    flex: 1;
    min-width: 0;
}

.mob-pdf-title {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-pdf-size {
    font-size: 9px;
    color: #64748b;
    margin-top: 1px;
}

.mob-pdf-download {
    font-size: 12px;
    color: #0284c7;
    flex-shrink: 0;
}

/* Footer Action Bar */
.mob-sheet-footer {
    padding: 10px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-shrink: 0;
}

.mob-sheet-btn {
    font-size: 11px;
    font-weight: 700;
    height: 32px;
    padding: 0 14px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
}

.mob-sheet-btn.primary {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

.mob-sheet-btn.primary:hover {
    background: #001833;
    color: #ffffff;
}

.mob-sheet-btn.secondary {
    background: #ffffff;
    color: #475569;
    border-color: #cbd5e1;
}

.mob-sheet-btn.secondary:hover {
    background: #f1f5f9;
    color: #1e293b;
}

/* Dark Theme Adjustments */
:root[data-theme="dark"] .mob-notif-page { color: #e2e8f0; }
:root[data-theme="dark"] .mob-search-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
:root[data-theme="dark"] .mob-pill-btn { background: #1e293b; border-color: #334155; color: #cbd5e1 !important; }
:root[data-theme="dark"] .mob-pill-btn.active { background: #0284c7; border-color: #0284c7; color: #ffffff !important; }
:root[data-theme="dark"] .mob-status-tabs { background: #1e293b; }
:root[data-theme="dark"] .mob-tab-btn { color: #94a3b8 !important; }
:root[data-theme="dark"] .mob-tab-btn.active { background: #0f172a; color: #38bdf8 !important; }
:root[data-theme="dark"] .mob-btn-hero.btn-mark-all { background: rgba(56, 189, 248, 0.2); }
:root[data-theme="dark"] .mob-notif-card { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-notif-card.is-unread { background: #172554; border-color: #1d4ed8; }
:root[data-theme="dark"] .mob-notif-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-notif-body { color: #cbd5e1; background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .mob-notif-footer { border-top-color: #334155; }
:root[data-theme="dark"] .mob-empty-box { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-empty-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-btn-action { background: #0f172a; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-notif-sheet { background: #1e293b; border-color: #38bdf8; }
:root[data-theme="dark"] .mob-sheet-fixed-meta { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .sheet-meta-pill { background: #1e293b; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .sheet-notif-title { color: #f8fafc; border-color: #334155; }
:root[data-theme="dark"] .sheet-notif-card { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .sheet-notif-text { color: #cbd5e1; }
:root[data-theme="dark"] .mob-sheet-footer { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .mob-sheet-btn.secondary { background: #1e293b; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-pagination-fixed-bar { background: rgba(15, 23, 42, 0.96); border-color: #334155; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45); }
:root[data-theme="dark"] .mob-pagination-info { color: #94a3b8; }
:root[data-theme="dark"] .mob-pagination-info b { color: #f8fafc; }
:root[data-theme="dark"] .mob-page-btn { background: #1e293b; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-page-btn.active { background: #0284c7 !important; border-color: #0284c7 !important; color: #ffffff !important; }
:root[data-theme="dark"] .mob-page-btn.disabled { background: #0f172a !important; border-color: #1e293b !important; color: #475569 !important; }
</style>
@endsection

@section('content')
<div class="mob-notif-page">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-bell" style="color: #38bdf8;"></i> Alerts & Notifications
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-tag"></i> {{ $selectedCategoryLabel }}
            </div>
        </div>

        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-inbox"></i> Total Alerts</span>
                <div class="mob-radar-val" id="statTotalVal">{{ $totalCount }}</div>
                <span class="mob-radar-meta">{{ $selectedCategoryLabel }}</span>
            </div>

            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-envelope-o"></i> Unread</span>
                <div class="mob-radar-val {{ $unreadCount > 0 ? 'danger-badge' : 'success-badge' }}" id="statUnreadVal">
                    {{ $unreadCount }}
                </div>
                <span class="mob-radar-meta">{{ $unreadCount > 0 ? 'Requires attention' : 'All caught up' }}</span>
            </div>
        </div>

        @if($totalCount > 0)
            <div class="mob-hero-actions">
                @if($unreadCount > 0)
                    <form id="formMarkAllRead" action="{{ route('student.attendance-notifications.mark-all-read') }}" method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="category" value="{{ $category }}">
                        <button type="submit" class="mob-btn-hero btn-mark-all" title="Mark all notifications as read">
                            <i class="fa fa-check-circle-o"></i> Mark all read
                        </button>
                    </form>
                @endif

                <form id="formClearAll" action="{{ route('student.attendance-notifications.clear-all') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="category" value="{{ $category }}">
                    <button type="submit" class="mob-btn-hero btn-clear-all" title="Clear all notifications in this section">
                        <i class="fa fa-trash-o"></i> Clear all
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- 2. Search & Filter Box --}}
    <div class="mob-filter-box">
        {{-- Search Input Row --}}
        <form action="{{ url('notificationFatchStudent') }}" method="GET" id="formNotifSearch">
            <input type="hidden" name="category" value="{{ $category }}">
            <input type="hidden" name="filter" value="{{ $filter }}">

            <div class="mob-search-row">
                <div class="mob-search-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text"
                           name="search"
                           id="notifSearchInput"
                           class="mob-search-input"
                           placeholder="Search alerts by title or keywords..."
                           value="{{ $currentSearch }}"
                           autocomplete="off">
                    <button type="button" id="btnNotifSearchClear" class="mob-search-clear" title="Clear search">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>
                <button type="submit" class="mob-search-submit">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
        </form>

        {{-- Horizontal Category Pills --}}
        <div class="mob-pill-scroll">
            @foreach($categoryMeta as $catKey => $meta)
                @php
                    $isActive = ($category === $catKey);
                    $badgeVal = (int) ($categoryCounts[$catKey] ?? 0);
                @endphp
                <a href="{{ $buildUrl(['category' => $catKey, 'filter' => $filter, 'search' => $currentSearch]) }}"
                   class="mob-pill-btn {{ $isActive ? 'active' : '' }}"
                   data-category="{{ $catKey }}">
                    <i class="fa {{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    <span class="mob-pill-badge" id="catBadge-{{ $catKey }}" style="{{ $badgeVal > 0 ? '' : 'display:none;' }}">{{ $badgeVal }}</span>
                </a>
            @endforeach
        </div>
        <script>
            (function () {
                var container = document.querySelector('.mob-pill-scroll');
                var active = container ? container.querySelector('.mob-pill-btn.active') : null;
                if (container && active) {
                    container.scrollLeft = Math.max(0, active.offsetLeft - container.offsetLeft);
                }
            })();
        </script>

        {{-- Status Filter (All / Unread / Read) --}}
        <div class="mob-status-tabs">
            <a href="{{ $buildUrl(['category' => $category, 'filter' => 'all', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'all' ? 'active' : '' }}">
                <span>All Alerts</span>
            </a>
            <a href="{{ $buildUrl(['category' => $category, 'filter' => 'unread', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'unread' ? 'active' : '' }}">
                <span>Unread</span>
                @if($unreadCount > 0)
                    <span class="mob-tab-count" id="tabUnreadBadge">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ $buildUrl(['category' => $category, 'filter' => 'read', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'read' ? 'active' : '' }}">
                <span>Read</span>
            </a>
        </div>
    </div>

    {{-- 3. Notification Stream --}}
    <div class="mob-notif-stream" id="notifStreamContainer">
        @forelse($notifications as $notification)
            @php
                $isUnread = (int) ($notification->message_seen ?? 0) === 0;
                $content = trim(strip_tags((string) ($notification->content ?? '')));
                $notificationType = strtolower((string) ($notification->type ?? ''));
                $typeConfig = [
                    'notice' => ['icon' => 'fa-bullhorn', 'label' => 'Notice', 'tone' => 'amber', 'badge_class' => 'badge-soft-amber'],
                    'exam_result' => ['icon' => 'fa-file-text-o', 'label' => 'Result', 'tone' => 'purple', 'badge_class' => 'badge-soft-purple'],
                    'fee_payment' => ['icon' => 'fa-inr', 'label' => 'Payment', 'tone' => 'green', 'badge_class' => 'badge-soft-success'],
                    'fee_revert' => ['icon' => 'fa-undo', 'label' => 'Fee Update', 'tone' => 'red', 'badge_class' => 'badge-soft-danger'],
                    'complaint_reply' => ['icon' => 'fa-reply', 'label' => 'Support', 'tone' => 'blue', 'badge_class' => 'badge-soft-info'],
                    'complaint_status' => ['icon' => 'fa-comments-o', 'label' => 'Complaint', 'tone' => 'blue', 'badge_class' => 'badge-soft-slate'],
                ];
                $config = $typeConfig[$notificationType] ?? ['icon' => 'fa-calendar-check-o', 'label' => 'Attendance', 'tone' => 'blue', 'badge_class' => 'badge-soft-info'];
                $createdAt = \Carbon\Carbon::parse($notification->created_at);
                $isLong = mb_strlen($content) > 130 || substr_count($content, "\n") > 1;

                // Attachment determination
                $attachRaw = $notification->attachment_path ?? $notification->image ?? null;
                $attachmentUrl = '';
                $attachmentName = '';
                if ($notificationType === 'notice' && $notification->managed_notice_id && $notification->attachment_path) {
                    $attachmentUrl = url('notice-management/' . $notification->managed_notice_id . '/attachment');
                    $attachmentName = $notification->attachment_name ?: 'Download Notice Attachment';
                } elseif (!empty($attachRaw)) {
                    $attachmentUrl = Str::startsWith($attachRaw, ['http://', 'https://', '/']) ? $attachRaw : asset($attachRaw);
                    $attachmentName = basename($attachRaw);
                }
            @endphp

            <div class="mob-notif-card {{ $isUnread ? 'is-unread' : '' }} tone-{{ $config['tone'] }}"
                 id="notifCard-{{ $notification->id }}"
                 data-title="{{ strtolower($notification->title ?? '') }}"
                 data-content="{{ strtolower($content) }}">

                {{-- Card Header --}}
                <div class="mob-notif-head">
                    <div class="mob-notif-icon tone-{{ $config['tone'] }}">
                        <i class="fa {{ $config['icon'] }}"></i>
                    </div>

                    <div class="mob-notif-info">
                        <div class="mob-notif-title-row">
                            <h3 class="mob-notif-title open-notif-trigger"
                                data-id="{{ $notification->id }}"
                                data-title="{{ e($notification->title ?: 'Notification') }}"
                                data-time="{{ $createdAt->format('d M Y, h:i A') }}"
                                data-type="{{ $config['label'] }}"
                                data-type-badge="{{ $config['badge_class'] }}"
                                data-icon="{{ $config['icon'] }}"
                                data-content="{{ e($content) }}"
                                data-attachment="{{ $attachmentUrl }}"
                                data-attachment-name="{{ $attachmentName }}"
                                data-unread="{{ $isUnread ? '1' : '0' }}"
                                data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}"
                                title="Tap to view full details">
                                {{ $notification->title ?: 'Notification' }}
                            </h3>
                            @if($isUnread)
                                <span class="mob-notif-badge-new" id="unreadBadge-{{ $notification->id }}">New</span>
                            @endif
                        </div>

                        <div class="mob-notif-meta-row">
                            <span class="mob-notif-type-tag">{{ $config['label'] }}</span>
                            <span class="mob-notif-time">
                                <i class="fa fa-clock-o"></i> {{ $createdAt->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Message Body --}}
                <div class="mob-notif-body {{ $isLong ? 'collapsed' : '' }}" id="notifBody-{{ $notification->id }}">
                    {{ $content !== '' ? $content : 'No message content available.' }}
                </div>

                {{-- Attachment Pill (if present) --}}
                @if(!empty($attachmentUrl))
                    <a href="{{ $attachmentUrl }}"
                       class="mob-notif-attach"
                       target="_blank"
                       download>
                        <i class="fa {{ preg_match('/\.(jpg|jpeg|png|webp)$/i', $attachmentUrl) ? 'fa-file-image-o text-success' : 'fa-file-pdf-o text-danger' }}"></i>
                        <span>{{ $attachmentName ?: 'Download Attachment' }}</span>
                        <i class="fa fa-download ms-auto text-muted"></i>
                    </a>
                @endif

                {{-- Card Footer & Actions --}}
                <div class="mob-notif-footer">
                    <span>
                        <i class="fa fa-calendar-o"></i> {{ $createdAt->format('d M Y, h:i A') }}
                    </span>

                    <div class="mob-notif-actions">
                        @if($isLong)
                            <button type="button"
                                    class="mob-btn-action btn-toggle-expand"
                                    data-id="{{ $notification->id }}"
                                    data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}"
                                    aria-expanded="false">
                                <i class="fa fa-angle-down"></i> Expand
                            </button>
                        @endif

                        @if($isUnread)
                            <button type="button"
                                    class="mob-btn-action btn-mark-read"
                                    id="btnMarkRead-{{ $notification->id }}"
                                    data-id="{{ $notification->id }}"
                                    data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}">
                                <i class="fa fa-check"></i> Read
                            </button>
                        @endif

                        <button type="button"
                                class="mob-btn-action btn-view-quick open-notif-trigger"
                                data-id="{{ $notification->id }}"
                                data-title="{{ e($notification->title ?: 'Notification') }}"
                                data-time="{{ $createdAt->format('d M Y, h:i A') }}"
                                data-type="{{ $config['label'] }}"
                                data-type-badge="{{ $config['badge_class'] }}"
                                data-icon="{{ $config['icon'] }}"
                                data-content="{{ e($content) }}"
                                data-attachment="{{ $attachmentUrl }}"
                                data-attachment-name="{{ $attachmentName }}"
                                data-unread="{{ $isUnread ? '1' : '0' }}"
                                data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="mob-empty-box">
                <div class="mob-empty-icon">
                    <i class="fa fa-bell-slash-o"></i>
                </div>
                <h3 class="mob-empty-title">No Notifications Found</h3>
                <p class="mob-empty-desc">
                    {{ $filter === 'all'
                        ? 'You do not have any alerts in '.$selectedCategoryLabel.'. New school announcements will appear here.'
                        : 'There are no '.$filter.' notifications in this section.' }}
                </p>
                @if($filter !== 'all' || $category !== 'all' || $currentSearch !== '')
                    <a href="{{ $buildUrl(['category' => 'all', 'filter' => 'all']) }}" class="mob-btn-reset">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- 4. Compact Fixed Bottom Pagination Bar (Positioned Directly Above Bottom Dock Footer) --}}
    @if($notifications->hasPages())
        @php
            $currentPage = $notifications->currentPage();
            $lastPage = $notifications->lastPage();
            $startPage = max(1, $currentPage - 1);
            $endPage = min($lastPage, $currentPage + 1);
            if ($currentPage === 1) {
                $endPage = min($lastPage, 3);
            } elseif ($currentPage === $lastPage) {
                $startPage = max(1, $lastPage - 2);
            }
            $pageRange = $notifications->appends(request()->query())->getUrlRange($startPage, $endPage);
        @endphp
        <div class="mob-pagination-fixed-bar">
            <div class="mob-pagination-info">
                Showing <b>{{ $notifications->firstItem() ?? 0 }}</b>-<b>{{ $notifications->lastItem() ?? 0 }}</b> of <b>{{ $notifications->total() }}</b>
            </div>
            <div class="mob-pagination-btns">
                {{-- Prev Button --}}
                @if($notifications->onFirstPage())
                    <span class="mob-page-btn disabled" aria-disabled="true">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $notifications->appends(request()->query())->previousPageUrl() }}"
                       class="mob-page-btn"
                       rel="prev"
                       title="Previous page">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Page Number Buttons --}}
                @foreach($pageRange as $pageNum => $pageUrl)
                    @if($pageNum == $currentPage)
                        <span class="mob-page-btn active">{{ $pageNum }}</span>
                    @else
                        <a href="{{ $pageUrl }}" class="mob-page-btn">{{ $pageNum }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($notifications->hasMorePages())
                    <a href="{{ $notifications->appends(request()->query())->nextPageUrl() }}"
                       class="mob-page-btn"
                       rel="next"
                       title="Next page">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @else
                    <span class="mob-page-btn disabled" aria-disabled="true">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif

</div>

{{-- ==========================================================================
     5. Mobile Custom Bottom Sheet Modal (Matching notices & homework sheets)
     ========================================================================== --}}
<div class="mob-notif-modal-backdrop" id="mobNotifBackdrop" onclick="closeNotifSheet()"></div>
<div class="mob-notif-sheet" id="mobNotifSheet" role="dialog" aria-modal="true" aria-labelledby="sheetNotifTitle">
    {{-- Header --}}
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-bell-o" id="sheetNotifIcon" style="color: #38bdf8;"></i>
            <span>Notification Details</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeNotifSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Pinned Meta Summary --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span id="sheetNotifTypeBadge" class="mob-type-badge badge-soft-info">Notification</span>
            <span id="sheetNotifTime" class="sheet-meta-pill"><i class="fa fa-clock-o"></i> <span>-</span></span>
            <span id="sheetNotifStatus" class="sheet-meta-pill sheet-status-read"><i class="fa fa-check text-success"></i> Read</span>
        </div>
    </div>

    {{-- Scrollable Body --}}
    <div class="mob-sheet-body">
        <h3 id="sheetNotifTitle" class="sheet-notif-title"></h3>
        <div class="sheet-notif-card">
            <div id="sheetNotifBody" class="sheet-notif-text"></div>
        </div>

        {{-- Attachment Download Pill if available --}}
        <div id="sheetNotifAttachment" class="sheet-attach-wrap" style="display: none;">
            <a id="sheetAttachmentLink" href="#" target="_blank" download class="mob-pdf-btn">
                <i class="fa fa-paperclip mob-pdf-icon" id="sheetAttachmentIcon"></i>
                <div class="mob-pdf-info">
                    <div class="mob-pdf-title" id="sheetAttachmentName">Attachment</div>
                    <div class="mob-pdf-size">Tap to view / download file</div>
                </div>
                <div class="mob-pdf-download">
                    <i class="fa fa-download"></i>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// Global functions for modal bottom sheet
function openNotifSheet(triggerEl) {
    if (!triggerEl) return;
    const id = triggerEl.getAttribute('data-id');
    const title = triggerEl.getAttribute('data-title') || 'Notification';
    const time = triggerEl.getAttribute('data-time') || '-';
    const type = triggerEl.getAttribute('data-type') || 'Notification';
    const typeBadge = triggerEl.getAttribute('data-type-badge') || 'badge-soft-info';
    const iconClass = triggerEl.getAttribute('data-icon') || 'fa-bell-o';
    const content = triggerEl.getAttribute('data-content') || '';
    const attachment = triggerEl.getAttribute('data-attachment') || '';
    const isUnread = triggerEl.getAttribute('data-unread') === '1';
    const readUrl = triggerEl.getAttribute('data-read-url');

    // Populate header icon & type badge
    const iconEl = document.getElementById('sheetNotifIcon');
    if (iconEl) iconEl.className = 'fa ' + iconClass;

    const typeEl = document.getElementById('sheetNotifTypeBadge');
    if (typeEl) {
        typeEl.className = 'mob-type-badge ' + typeBadge;
        typeEl.innerHTML = '<i class="fa ' + iconClass + '"></i> ' + type;
    }

    // Populate time & status
    const timeEl = document.getElementById('sheetNotifTime');
    if (timeEl) timeEl.innerHTML = '<i class="fa fa-clock-o"></i> <span>' + time + '</span>';

    const statusEl = document.getElementById('sheetNotifStatus');
    if (statusEl) {
        statusEl.className = 'sheet-meta-pill ' + (isUnread ? 'sheet-status-unread' : 'sheet-status-read');
        statusEl.innerHTML = isUnread
            ? '<i class="fa fa-circle text-primary" style="font-size:7px;"></i> Unread'
            : '<i class="fa fa-check text-success"></i> Read';
    }

    // Populate title & body text
    const titleEl = document.getElementById('sheetNotifTitle');
    if (titleEl) titleEl.textContent = title;

    const bodyEl = document.getElementById('sheetNotifBody');
    if (bodyEl) bodyEl.textContent = content;

    // Attachment
    const attachWrap = document.getElementById('sheetNotifAttachment');
    const attachLink = document.getElementById('sheetAttachmentLink');
    const attachName = document.getElementById('sheetAttachmentName');
    const attachIcon = document.getElementById('sheetAttachmentIcon');
    if (attachWrap && attachLink) {
        if (attachment && attachment.trim() !== '') {
            attachWrap.style.display = 'block';
            attachLink.href = attachment;
            if (attachName) attachName.textContent = attachmentName || attachment.split('/').pop() || 'Attachment';
            if (attachIcon) {
                const isImg = /\.(jpg|jpeg|png|webp|gif)$/i.test(attachment);
                const isPdf = /\.pdf$/i.test(attachment);
                attachIcon.className = 'mob-pdf-icon fa ' + (isImg ? 'fa-file-image-o text-success' : (isPdf ? 'fa-file-pdf-o text-danger' : 'fa-paperclip text-primary'));
            }
        } else {
            attachWrap.style.display = 'none';
        }
    }

    // Open sheet & backdrop
    const backdrop = document.getElementById('mobNotifBackdrop');
    const sheet = document.getElementById('mobNotifSheet');
    if (backdrop) backdrop.classList.add('show');
    if (sheet) sheet.classList.add('show');
    document.body.classList.add('mob-sheet-lock');

    // Optimistically mark as read
    if (isUnread && readUrl) {
        triggerEl.setAttribute('data-unread', '0');
        if (statusEl) {
            statusEl.className = 'sheet-meta-pill sheet-status-read';
            statusEl.innerHTML = '<i class="fa fa-check text-success"></i> Read';
        }
        if (typeof window.performMarkRead === 'function') {
            window.performMarkRead(id, readUrl);
        }
    }
}

function closeNotifSheet() {
    const backdrop = document.getElementById('mobNotifBackdrop');
    const sheet = document.getElementById('mobNotifSheet');
    if (backdrop) backdrop.classList.remove('show');
    if (sheet) sheet.classList.remove('show');
    document.body.classList.remove('mob-sheet-lock');
}

document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = @json(csrf_token());
    const searchInput = document.getElementById('notifSearchInput');
    const searchClear = document.getElementById('btnNotifSearchClear');
    const cards = document.querySelectorAll('.mob-notif-card');

    // Auto-scroll active category pill to left start
    const pillContainer = document.querySelector('.mob-pill-scroll');
    if (pillContainer) {
        const activePill = pillContainer.querySelector('.mob-pill-btn.active');
        if (activePill) {
            pillContainer.scrollLeft = Math.max(0, activePill.offsetLeft - pillContainer.offsetLeft);
        }
    }

    // 1. Live Instant Client Search Filter (Zero lag)
    let searchDebounceTimer = null;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            if (searchClear) {
                searchClear.style.display = query !== '' ? 'flex' : 'none';
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function () {
                cards.forEach(function (card) {
                    const title = card.getAttribute('data-title') || '';
                    const content = card.getAttribute('data-content') || '';
                    if (query === '' || title.includes(query) || content.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 120);
        });

        if (searchClear) {
            searchClear.addEventListener('click', function () {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }

    // 2. Optimistic AJAX Mark Read Handler
    window.performMarkRead = function (notificationId, readUrl) {
        const card = document.getElementById('notifCard-' + notificationId);
        if (card) {
            card.classList.remove('is-unread');
        }
        const badge = document.getElementById('unreadBadge-' + notificationId);
        if (badge) badge.remove();
        const markBtn = document.getElementById('btnMarkRead-' + notificationId);
        if (markBtn) markBtn.remove();

        // Decrement Stat Badges
        const unreadStat = document.getElementById('statUnreadVal');
        var newCount = 0;
        if (unreadStat) {
            newCount = Math.max(0, parseInt(unreadStat.textContent.trim(), 10) - 1);
            unreadStat.textContent = newCount;
            if (newCount === 0) {
                unreadStat.classList.remove('danger-badge');
                unreadStat.classList.add('success-badge');
            }
        }
        const tabBadge = document.getElementById('tabUnreadBadge');
        if (tabBadge) {
            const currentTab = Math.max(0, parseInt(tabBadge.textContent.trim(), 10) - 1);
            if (currentTab > 0) {
                tabBadge.textContent = currentTab;
            } else {
                tabBadge.remove();
            }
        }

        // Optimistically update global header & footer dock badges
        var headerBadge = document.getElementById('headerAlertsBadge');
        if (headerBadge) {
            var currentHeader = Math.max(0, (parseInt(headerBadge.textContent.trim(), 10) || 0) - 1);
            if (typeof window.updateStudentAlertBadges === 'function') {
                window.updateStudentAlertBadges(currentHeader);
            }
        }

        return fetch(readUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (res) {
            return res.json();
        }).then(function (data) {
            if (data && data.ok) {
                if (typeof data.unread_count !== 'undefined') {
                    if (typeof window.updateStudentAlertBadges === 'function') {
                        window.updateStudentAlertBadges(data.unread_count);
                    }
                }
                if (data.category_counts) {
                    for (var cat in data.category_counts) {
                        var catEl = document.getElementById('catBadge-' + cat);
                        if (catEl) {
                            var val = parseInt(data.category_counts[cat], 10) || 0;
                            catEl.textContent = val;
                            catEl.style.display = val > 0 ? '' : 'none';
                        }
                    }
                }
            }
        }).catch(function (err) {
            console.error('Mark read failed:', err);
        });
    };

    // Single Mark Read button click
    document.querySelectorAll('.btn-mark-read').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            this.disabled = true;
            window.performMarkRead(this.dataset.id, this.dataset.readUrl);
        });
    });

    // Expand / Collapse long notification text
    document.querySelectorAll('.btn-toggle-expand').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const body = document.getElementById('notifBody-' + id);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            if (body) {
                body.classList.toggle('collapsed', isExpanded);
            }
            this.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            this.innerHTML = isExpanded
                ? '<i class="fa fa-angle-down"></i> Expand'
                : '<i class="fa fa-angle-up"></i> Collapse';

            if (isExpanded === false && this.dataset.readUrl) {
                window.performMarkRead(id, this.dataset.readUrl);
            }
        });
    });

    // 3. Quick View Sheet Triggers
    document.querySelectorAll('.open-notif-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            openNotifSheet(this);
        });
    });

    // ESC Key to close modal bottom sheet
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeNotifSheet();
        }
    });

    // 4. Mark All Read with Confirmation
    const formMarkAll = document.getElementById('formMarkAllRead');
    if (formMarkAll) {
        formMarkAll.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (window.StudentModal && typeof window.StudentModal.confirm === 'function') {
                const result = await StudentModal.confirm({
                    type: 'info',
                    title: 'Mark All as Read?',
                    message: 'Do you want to mark all unread notifications in this section as read?',
                    confirmText: 'Yes, Mark Read',
                    cancelText: 'Cancel'
                });
                if (result && (result.isConfirmed || result === true)) {
                    formMarkAll.submit();
                }
            } else if (confirm('Mark all unread notifications as read?')) {
                formMarkAll.submit();
            }
        });
    }

    // 5. Clear All with Confirmation
    const formClear = document.getElementById('formClearAll');
    if (formClear) {
        formClear.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (window.StudentModal && typeof window.StudentModal.confirm === 'function') {
                const result = await StudentModal.confirm({
                    type: 'warning',
                    title: 'Clear Notifications?',
                    message: 'Are you sure you want to clear notifications in this section? This action cannot be undone.',
                    confirmText: 'Yes, Clear All',
                    cancelText: 'Cancel',
                    danger: true
                });
                if (result && (result.isConfirmed || result === true)) {
                    formClear.submit();
                }
            } else if (confirm('Are you sure you want to clear all notifications in this section?')) {
                formClear.submit();
            }
        });
    }
});
</script>
@endsection
