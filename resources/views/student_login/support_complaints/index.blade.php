@php
    $getUser = Helper::getUser();
    $studentName = Session::get('first_name') ?: ($getUser->first_name ?? 'Student');
    $className = optional($getUser)->ClassTypes->name ?? 'Student';

    $totalComplaints = (int) ($complaintCounts->total_count ?? 0);
    $activeComplaints = (int) ($complaintCounts->active_count ?? 0);
    $awaitingComplaints = (int) ($complaintCounts->awaiting_count ?? 0);
    $resolvedComplaints = (int) ($complaintCounts->resolved_count ?? 0);

    $category = strtolower(trim((string) ($category ?? 'all')));
    $filter = strtolower(trim((string) ($filter ?? 'all')));
    $currentSearch = (string) ($search ?? request('search', ''));

    $allCategories = \App\Models\SupportComplaint::CATEGORIES;
    $categoryIcons = [
        'academic'   => 'fa-graduation-cap',
        'fees'       => 'fa-inr',
        'attendance' => 'fa-calendar-check-o',
        'transport'  => 'fa-bus',
        'facility'   => 'fa-building-o',
        'staff'      => 'fa-user-o',
        'technical'  => 'fa-laptop',
        'other'      => 'fa-comments-o',
    ];

    $categoryMeta = [
        'all' => ['label' => 'All Categories', 'icon' => 'fa-th-large', 'count' => $totalComplaints],
    ];
    foreach ($allCategories as $catKey => $catLabel) {
        $categoryMeta[$catKey] = [
            'label' => $catLabel,
            'icon'  => $categoryIcons[$catKey] ?? 'fa-tag',
            'count' => (int) ($categoryCounts[$catKey] ?? 0),
        ];
    }

    // If a specific category is selected, bring it to the front (left starting position)
    if ($category !== 'all' && isset($categoryMeta[$category])) {
        $selectedCat = [$category => $categoryMeta[$category]];
        unset($categoryMeta[$category]);
        $categoryMeta = $selectedCat + $categoryMeta;
    }

    $buildUrl = function (array $params) {
        $params = array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
        return url('student-complaints' . (count($params) ? '?' . http_build_query($params) : ''));
    };

    $statusMeta = [
        'open'          => ['label' => 'Open', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-clock-o'],
        'acknowledged'  => ['label' => 'Acknowledged', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-check'],
        'in_progress'   => ['label' => 'In Progress', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-spinner fa-spin'],
        'awaiting_user' => ['label' => 'Awaiting Your Reply', 'badge_class' => 'badge-soft-amber', 'tone' => 'amber', 'icon' => 'fa-reply'],
        'resolved'      => ['label' => 'Resolved', 'badge_class' => 'badge-soft-success', 'tone' => 'green', 'icon' => 'fa-check-circle'],
        'closed'        => ['label' => 'Closed', 'badge_class' => 'badge-soft-slate', 'tone' => 'slate', 'icon' => 'fa-lock'],
        'reopened'      => ['label' => 'Reopened', 'badge_class' => 'badge-soft-purple', 'tone' => 'purple', 'icon' => 'fa-refresh'],
    ];

    $priorityMeta = [
        'low'    => ['label' => 'Low', 'class' => 'priority-low'],
        'medium' => ['label' => 'Medium', 'class' => 'priority-medium'],
        'high'   => ['label' => 'High', 'class' => 'priority-high'],
        'urgent' => ['label' => 'Urgent', 'class' => 'priority-urgent'],
    ];
@endphp
@extends('student_login.layout.app')

@section('title', 'Help & Support')
@section('page_title', 'HELP & SUPPORT')
@section('page_sub', $studentName . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE COMPLAINTS & HELP DESK
   Exact Layout, Typography & Cohesive Theme System
   ========================================================================== */

.mob-complaint-page {
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
    background: radial-gradient(circle, rgba(56, 189, 248, 0.16) 0%, rgba(56, 189, 248, 0) 70%);
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

/* 4-Metric Glance Grid (2x2) */
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

.mob-radar-val.danger-badge { color: #f87171; }
.mob-radar-val.warning-badge { color: #fcd34d; }
.mob-radar-val.success-badge { color: #4ade80; }

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
    justify-content: space-between;
    gap: 8px;
    position: relative;
    z-index: 1;
}

.mob-hero-desc {
    font-size: 10px;
    color: #cbd5e1;
    margin: 0;
    line-height: 1.3;
}

.mob-btn-raise {
    background: #0284c7;
    border: 1px solid #38bdf8;
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
    transition: all 0.15s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.mob-btn-raise:hover {
    background: #0369a1;
    color: #ffffff !important;
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

/* Status Segmented Tabs */
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
    background: #0284c7;
    color: #ffffff;
    font-size: 8px;
    padding: 0 4px;
    border-radius: 8px;
    font-weight: 800;
}

.mob-tab-count.amber {
    background: #f59e0b;
}

/* 3. Complaint Stream Cards */
.mob-complaint-stream {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mob-complaint-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #94a3b8;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.mob-complaint-card.tone-blue   { border-left-color: #0284c7; }
.mob-complaint-card.tone-green  { border-left-color: #10b981; }
.mob-complaint-card.tone-amber  { border-left-color: #f59e0b; }
.mob-complaint-card.tone-purple { border-left-color: #8b5cf6; }
.mob-complaint-card.tone-slate  { border-left-color: #64748b; }

.mob-complaint-card.is-waiting {
    background: #fffdfa;
    border-top: 1px solid #fde68a;
    border-right: 1px solid #fde68a;
    border-bottom: 1px solid #fde68a;
}

.mob-complaint-head {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 6px;
}

.mob-complaint-icon {
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

.mob-complaint-icon.tone-blue   { background: #0284c7; }
.mob-complaint-icon.tone-green  { background: #10b981; }
.mob-complaint-icon.tone-amber  { background: #f59e0b; }
.mob-complaint-icon.tone-purple { background: #8b5cf6; }
.mob-complaint-icon.tone-slate  { background: #64748b; }

.mob-complaint-info {
    flex: 1;
    min-width: 0;
}

.mob-complaint-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.mob-ticket-badge {
    font-size: 8.5px;
    font-weight: 800;
    color: #0284c7;
    background: #e0f2fe;
    padding: 1px 5px;
    border-radius: 2px;
    letter-spacing: 0.3px;
    font-family: monospace;
}

.mob-status-pill {
    font-size: 8px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.badge-soft-info    { background: #e0f2fe; color: #0284c7; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.badge-soft-amber   { background: #fef3c7; color: #b45309; }
.badge-soft-purple  { background: #ede9fe; color: #6d28d9; }
.badge-soft-slate   { background: #f1f5f9; color: #475569; }

.mob-complaint-title {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    margin: 2px 0 3px;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-complaint-title a {
    color: inherit;
    text-decoration: none;
}

.mob-complaint-title a:hover {
    color: #0284c7;
}

.mob-complaint-tags-row {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.mob-cat-tag {
    font-size: 9px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    padding: 1.5px 5px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.mob-priority-tag {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1.5px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.priority-low    { background: #f1f5f9; color: #475569; }
.priority-medium { background: #fef3c7; color: #b45309; }
.priority-high   { background: #ffedd5; color: #c2410c; }
.priority-urgent { background: #fee2e2; color: #b91c1c; }

/* Message / Latest Reply Preview Box */
.mob-complaint-preview {
    font-size: 11px;
    line-height: 1.5;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.mob-complaint-preview .reply-prefix {
    font-weight: 700;
    color: #002C54;
}

/* Card Footer */
.mob-complaint-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 4px;
    border-top: 1px solid #f1f5f9;
    font-size: 9.5px;
    color: #64748b;
}

.mob-complaint-meta-strip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
}

.mob-complaint-actions {
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
    text-decoration: none !important;
    transition: all 0.12s ease;
    line-height: 1.25;
}

.mob-btn-action:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.mob-btn-action.btn-view {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #0284c7;
}

.mob-btn-action.btn-view:hover {
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

/* ==========================================================================
   4. Fixed Bottom Pagination Bar (Directly Above Bottom Dock Footer)
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
   5. Native Slide-Up Bottom Sheet: Raise New Complaint
   ========================================================================== */
.mob-complaint-modal-backdrop {
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

.mob-complaint-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}

.mob-complaint-sheet {
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
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}

.mob-complaint-sheet.show {
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

.mob-sheet-body {
    padding: 14px 14px 20px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

.mob-form-group {
    margin-bottom: 11px;
}

.mob-form-label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}

.mob-form-label .req {
    color: #ef4444;
}

.mob-form-control, .mob-form-select {
    width: 100%;
    height: 32px;
    padding: 0 9px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11.5px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    box-sizing: border-box;
}

.mob-form-control:focus, .mob-form-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.mob-form-textarea {
    width: 100%;
    min-height: 90px;
    padding: 8px 9px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11.5px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    resize: vertical;
    box-sizing: border-box;
    line-height: 1.5;
}

.mob-form-textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.mob-radio-toggle {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}

.mob-radio-opt {
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 5px 8px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: #f8fafc;
    transition: all 0.15s ease;
}

.mob-radio-opt input[type="radio"] {
    display: none;
}

.mob-radio-opt.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

.mob-file-box {
    border: 1px dashed #cbd5e1;
    border-radius: 3px;
    padding: 8px 10px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mob-file-box input[type="file"] {
    font-size: 10px;
    color: #64748b;
    width: 100%;
}

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

/* Dark Theme Adjustments */
:root[data-theme="dark"] .mob-complaint-page { color: #e2e8f0; }
:root[data-theme="dark"] .mob-filter-box { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-search-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
:root[data-theme="dark"] .mob-pill-btn { background: #1e293b; border-color: #334155; color: #cbd5e1 !important; }
:root[data-theme="dark"] .mob-pill-btn.active { background: #0284c7; border-color: #0284c7; color: #ffffff !important; }
:root[data-theme="dark"] .mob-status-tabs { background: #0f172a; }
:root[data-theme="dark"] .mob-tab-btn { color: #94a3b8 !important; }
:root[data-theme="dark"] .mob-tab-btn.active { background: #1e293b; color: #38bdf8 !important; }
:root[data-theme="dark"] .mob-complaint-card { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-complaint-card.is-waiting { background: #262117; border-color: #d97706; }
:root[data-theme="dark"] .mob-complaint-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-cat-tag { background: #0f172a; color: #cbd5e1; }
:root[data-theme="dark"] .mob-complaint-preview { background: #0f172a; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-complaint-preview .reply-prefix { color: #38bdf8; }
:root[data-theme="dark"] .mob-complaint-footer { border-top-color: #334155; }
:root[data-theme="dark"] .mob-btn-action { background: #0f172a; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-empty-box { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-empty-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-pagination-fixed-bar { background: rgba(15, 23, 42, 0.96); border-color: #334155; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45); }
:root[data-theme="dark"] .mob-pagination-info { color: #94a3b8; }
:root[data-theme="dark"] .mob-pagination-info b { color: #f8fafc; }
:root[data-theme="dark"] .mob-page-btn { background: #1e293b; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-page-btn.active { background: #0284c7 !important; border-color: #0284c7 !important; color: #ffffff !important; }
:root[data-theme="dark"] .mob-page-btn.disabled { background: #0f172a !important; border-color: #1e293b !important; color: #475569 !important; }
:root[data-theme="dark"] .mob-complaint-sheet { background: #1e293b; border-color: #38bdf8; }
:root[data-theme="dark"] .mob-form-control, :root[data-theme="dark"] .mob-form-select, :root[data-theme="dark"] .mob-form-textarea { background: #0f172a; border-color: #334155; color: #f8fafc; }
:root[data-theme="dark"] .mob-form-label { color: #cbd5e1; }
:root[data-theme="dark"] .mob-radio-opt { background: #0f172a; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-radio-opt.active { background: #0284c7; border-color: #0284c7; color: #ffffff; }
:root[data-theme="dark"] .mob-file-box { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .mob-sheet-footer { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .mob-sheet-btn.secondary { background: #1e293b; border-color: #334155; color: #cbd5e1; }
</style>
@endsection

@section('content')
<div class="mob-complaint-page">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-life-ring" style="color: #38bdf8;"></i> Help & Support Desk
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-ticket"></i> Complaints
            </div>
        </div>

        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-inbox"></i> Total Raised</span>
                <div class="mob-radar-val" id="statTotalVal">{{ $totalComplaints }}</div>
                <span class="mob-radar-meta">All tickets</span>
            </div>

            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-spinner"></i> In Progress</span>
                <div class="mob-radar-val {{ $activeComplaints > 0 ? 'warning-badge' : 'success-badge' }}">
                    {{ $activeComplaints }}
                </div>
                <span class="mob-radar-meta">Active issues</span>
            </div>
        </div>

        <div class="mob-hero-actions">
            <p class="mob-hero-desc">
                {{ $awaitingComplaints > 0 ? '⚠️ You have ' . $awaitingComplaints . ' ticket(s) awaiting your reply.' : 'Raise concerns & track real-time resolution from school.' }}
            </p>
            <button type="button" class="mob-btn-raise" onclick="openNewComplaintSheet()">
                <i class="fa fa-plus-circle"></i> New Complaint
            </button>
        </div>
    </div>

    {{-- 2. Compact Search & Filter Toolbar --}}
    <div class="mob-filter-box">
        <form action="{{ url('student-complaints') }}" method="GET" id="formComplaintSearch">
            <input type="hidden" name="status" value="{{ $filter }}">
            <input type="hidden" name="category" value="{{ $category }}">

            <div class="mob-search-row">
                <div class="mob-search-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text"
                           name="search"
                           id="complaintSearchInput"
                           class="mob-search-input"
                           placeholder="Search tickets by ticket # or subject..."
                           value="{{ $currentSearch }}"
                           autocomplete="off">
                    <button type="button" id="btnComplaintSearchClear" class="mob-search-clear" title="Clear search">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>
                <button type="submit" class="mob-search-submit">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
        </form>

        {{-- Horizontal Scrollable Category Pills --}}
        <div class="mob-pill-scroll">
            @foreach($categoryMeta as $catKey => $meta)
                @php
                    $isActive = ($category === $catKey);
                    $badgeVal = $meta['count'];
                @endphp
                <a href="{{ $buildUrl(['category' => $catKey, 'status' => $filter, 'search' => $currentSearch]) }}"
                   class="mob-pill-btn {{ $isActive ? 'active' : '' }}">
                    <i class="fa {{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    @if($badgeVal > 0)
                        <span class="mob-pill-badge">{{ $badgeVal }}</span>
                    @endif
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

        {{-- Status Filter Tabs --}}
        <div class="mob-status-tabs">
            <a href="{{ $buildUrl(['category' => $category, 'status' => 'all', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'all' ? 'active' : '' }}">
                <span>All ({{ $totalComplaints }})</span>
            </a>
            <a href="{{ $buildUrl(['category' => $category, 'status' => 'active', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'active' ? 'active' : '' }}">
                <span>Active</span>
                @if($activeComplaints > 0)
                    <span class="mob-tab-count">{{ $activeComplaints }}</span>
                @endif
            </a>
            <a href="{{ $buildUrl(['category' => $category, 'status' => 'awaiting', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'awaiting' ? 'active' : '' }}">
                <span>Need Reply</span>
                @if($awaitingComplaints > 0)
                    <span class="mob-tab-count amber">{{ $awaitingComplaints }}</span>
                @endif
            </a>
            <a href="{{ $buildUrl(['category' => $category, 'status' => 'resolved', 'search' => $currentSearch]) }}"
               class="mob-tab-btn {{ $filter === 'resolved' ? 'active' : '' }}">
                <span>Resolved</span>
            </a>
        </div>
    </div>

    {{-- 3. Complaint Stream Cards --}}
    <div class="mob-complaint-stream" id="complaintStreamContainer">
        @forelse($complaints as $complaint)
            @php
                $statusConfig = $statusMeta[$complaint->status] ?? [
                    'label' => ucfirst(str_replace('_', ' ', $complaint->status)),
                    'badge_class' => 'badge-soft-info',
                    'tone' => 'blue',
                    'icon' => 'fa-clock-o',
                ];
                $priorityConfig = $priorityMeta[$complaint->priority] ?? ['label' => ucfirst($complaint->priority), 'class' => 'priority-medium'];
                $catLabel = $allCategories[$complaint->category] ?? ucfirst($complaint->category);
                $catIcon = $categoryIcons[$complaint->category] ?? 'fa-tag';
                $isAwaiting = ($complaint->status === 'awaiting_user');
                $lastReply = $complaint->replies->first();
                $latestText = $lastReply ? $lastReply->message : ($complaint->message ?? '');
                $latestSender = $lastReply ? (in_array($lastReply->sender_type, ['student', 'parent']) ? 'You' : 'School Admin') : '';
                $hasAttachment = $complaint->replies->whereNotNull('attachment_path')->count() > 0;
                $replyCount = $complaint->replies_count;
                $timeAgo = optional($complaint->last_replied_at ?: $complaint->created_at)->diffForHumans();
            @endphp

            <div class="mob-complaint-card tone-{{ $statusConfig['tone'] }} {{ $isAwaiting ? 'is-waiting' : '' }}"
                 id="complaintCard-{{ $complaint->id }}"
                 data-ticket="{{ strtolower($complaint->ticket_no) }}"
                 data-subject="{{ strtolower($complaint->subject) }}"
                 data-category="{{ strtolower($catLabel) }}">

                {{-- Header --}}
                <div class="mob-complaint-head">
                    <div class="mob-complaint-icon tone-{{ $statusConfig['tone'] }}">
                        <i class="fa {{ $catIcon }}"></i>
                    </div>

                    <div class="mob-complaint-info">
                        <div class="mob-complaint-top-row">
                            <span class="mob-ticket-badge">{{ $complaint->ticket_no }}</span>
                            <span class="mob-status-pill {{ $statusConfig['badge_class'] }}">
                                <i class="fa {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        <h3 class="mob-complaint-title">
                            <a href="{{ url('student-complaints/' . $complaint->id) }}" title="{{ $complaint->subject }}">
                                {{ $complaint->subject }}
                            </a>
                        </h3>

                        <div class="mob-complaint-tags-row">
                            <span class="mob-cat-tag">
                                <i class="fa {{ $catIcon }}"></i> {{ $catLabel }}
                            </span>
                            <span class="mob-priority-tag {{ $priorityConfig['class'] }}">
                                <i class="fa fa-flag"></i> {{ $priorityConfig['label'] }}
                            </span>
                            <span style="font-size: 8.5px; color: #94a3b8;">
                                By {{ $complaint->submitted_as === 'parent' ? 'Parent' : 'Student' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Latest Message Snippet --}}
                @if(!empty($latestText))
                    <div class="mob-complaint-preview">
                        @if($latestSender)
                            <span class="reply-prefix">{{ $latestSender }}: </span>
                        @endif
                        {{ Str::limit(strip_tags($latestText), 140) }}
                    </div>
                @endif

                {{-- Footer & Actions --}}
                <div class="mob-complaint-footer">
                    <div class="mob-complaint-meta-strip">
                        <span><i class="fa fa-comments-o"></i> {{ $replyCount }} {{ Str::plural('reply', $replyCount) }}</span>
                        <span><i class="fa fa-clock-o"></i> {{ $timeAgo }}</span>
                        @if($hasAttachment)
                            <span title="Contains file attachment"><i class="fa fa-paperclip text-primary"></i> File</span>
                        @endif
                    </div>

                    <div class="mob-complaint-actions">
                        @if(!in_array($complaint->status, ['resolved', 'closed']))
                            <a href="{{ url('student-complaints/' . $complaint->id . '#replyBox') }}"
                               class="mob-btn-action"
                               title="Post a reply">
                                <i class="fa fa-reply"></i> Reply
                            </a>
                        @endif
                        <a href="{{ url('student-complaints/' . $complaint->id) }}"
                           class="mob-btn-action btn-view"
                           title="View complete conversation">
                            <i class="fa fa-eye"></i> View & Track
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="mob-empty-box">
                <div class="mob-empty-icon">
                    <i class="fa fa-smile-o"></i>
                </div>
                <h3 class="mob-empty-title">No Complaints Found</h3>
                <p class="mob-empty-desc">
                    {{ $filter === 'all'
                        ? 'You do not have any registered complaints or support requests in this section.'
                        : 'No complaints match the selected filter.' }}
                </p>
                <button type="button" class="btn btn-sm btn-primary" onclick="openNewComplaintSheet()" style="font-size: 11px; font-weight: 700; border-radius: 4px;">
                    <i class="fa fa-plus-circle me-1"></i> Raise a Complaint
                </button>
            </div>
        @endforelse
    </div>

    {{-- 4. Compact Fixed Bottom Pagination Bar (Above Bottom Dock Footer) --}}
    @if($complaints->hasPages())
        @php
            $currentPage = $complaints->currentPage();
            $lastPage = $complaints->lastPage();
            $startPage = max(1, $currentPage - 1);
            $endPage = min($lastPage, $currentPage + 1);
            if ($currentPage === 1) {
                $endPage = min($lastPage, 3);
            } elseif ($currentPage === $lastPage) {
                $startPage = max(1, $lastPage - 2);
            }
            $pageRange = $complaints->appends(request()->query())->getUrlRange($startPage, $endPage);
        @endphp
        <div class="mob-pagination-fixed-bar">
            <div class="mob-pagination-info">
                Showing <b>{{ $complaints->firstItem() ?? 0 }}</b>-<b>{{ $complaints->lastItem() ?? 0 }}</b> of <b>{{ $complaints->total() }}</b>
            </div>
            <div class="mob-pagination-btns">
                {{-- Prev Button --}}
                @if($complaints->onFirstPage())
                    <span class="mob-page-btn disabled" aria-disabled="true">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $complaints->appends(request()->query())->previousPageUrl() }}"
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
                @if($complaints->hasMorePages())
                    <a href="{{ $complaints->appends(request()->query())->nextPageUrl() }}"
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
     5. Native Slide-Up Bottom Sheet: Raise New Complaint
     ========================================================================== --}}
<div class="mob-complaint-modal-backdrop" id="mobNewComplaintBackdrop" onclick="closeNewComplaintSheet()"></div>
<div class="mob-complaint-sheet" id="mobNewComplaintSheet" role="dialog" aria-modal="true" aria-labelledby="sheetNewComplaintTitle">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-plus-circle" style="color: #38bdf8;"></i>
            <span id="sheetNewComplaintTitle">Raise a New Complaint</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeNewComplaintSheet()" aria-label="Close">&times;</button>
    </div>

    <form id="formQuickComplaint" action="{{ url('student-complaints') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <div class="mob-sheet-body">
            {{-- Submitting As --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Submitting As <span class="req">*</span></label>
                <div class="mob-radio-toggle">
                    <label class="mob-radio-opt active" id="lblSubmitParent">
                        <input type="radio" name="submitted_as" value="parent" checked onchange="toggleSubmitAs(this)">
                        <i class="fa fa-users"></i> Parent / Guardian
                    </label>
                    <label class="mob-radio-opt" id="lblSubmitStudent">
                        <input type="radio" name="submitted_as" value="student" onchange="toggleSubmitAs(this)">
                        <i class="fa fa-user"></i> Student
                    </label>
                </div>
            </div>

            {{-- Subject --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Concern Subject <span class="req">*</span></label>
                <input type="text"
                       name="subject"
                       class="mob-form-control"
                       placeholder="E.g. Bus delayed in morning route / Fee receipt issue"
                       maxlength="255"
                       required>
            </div>

            {{-- Category & Priority in 2 Columns --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div class="mob-form-group">
                    <label class="mob-form-label">Category <span class="req">*</span></label>
                    <select name="category" class="mob-form-select" required>
                        @foreach(\App\Models\SupportComplaint::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label">Priority <span class="req">*</span></label>
                    <select name="priority" class="mob-form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            {{-- Complaint Message Details --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Details / Explanation <span class="req">*</span></label>
                <textarea name="message"
                          class="mob-form-textarea"
                          rows="4"
                          placeholder="Clearly describe your problem, when it happened, and what resolution you expect..."
                          maxlength="10000"
                          required></textarea>
            </div>

            {{-- Attachment (Optional) --}}
            <div class="mob-form-group" style="margin-bottom: 4px;">
                <label class="mob-form-label">
                    Attachment <span style="font-weight: normal; color: #94a3b8;">(Optional, PDF / Image max 10MB)</span>
                </label>
                <div class="mob-file-box">
                    <i class="fa fa-paperclip text-muted"></i>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </div>

        <div class="mob-sheet-footer">
            <button type="button" class="mob-sheet-btn secondary" onclick="closeNewComplaintSheet()">
                Cancel
            </button>
            <button type="submit" class="mob-sheet-btn primary" id="btnSubmitQuickComplaint">
                <i class="fa fa-paper-plane"></i> Submit Ticket
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@parent
<script>
// Open / Close Raise Complaint Sheet
function openNewComplaintSheet() {
    const backdrop = document.getElementById('mobNewComplaintBackdrop');
    const sheet = document.getElementById('mobNewComplaintSheet');
    if (backdrop) backdrop.classList.add('show');
    if (sheet) sheet.classList.add('show');
    document.body.classList.add('mob-sheet-lock');
}

function closeNewComplaintSheet() {
    const backdrop = document.getElementById('mobNewComplaintBackdrop');
    const sheet = document.getElementById('mobNewComplaintSheet');
    if (backdrop) backdrop.classList.remove('show');
    if (sheet) sheet.classList.remove('show');
    document.body.classList.remove('mob-sheet-lock');
}

function toggleSubmitAs(radio) {
    document.getElementById('lblSubmitParent').classList.toggle('active', radio.value === 'parent');
    document.getElementById('lblSubmitStudent').classList.toggle('active', radio.value === 'student');
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('complaintSearchInput');
    const searchClear = document.getElementById('btnComplaintSearchClear');
    const cards = document.querySelectorAll('.mob-complaint-card');

    // 1. Live Client Search Filter
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
                    const ticket = card.getAttribute('data-ticket') || '';
                    const subject = card.getAttribute('data-subject') || '';
                    const category = card.getAttribute('data-category') || '';
                    if (query === '' || ticket.includes(query) || subject.includes(query) || category.includes(query)) {
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

    // Auto-scroll active category pill to left
    const pillContainer = document.querySelector('.mob-pill-scroll');
    if (pillContainer) {
        const activePill = pillContainer.querySelector('.mob-pill-btn.active');
        if (activePill) {
            pillContainer.scrollLeft = Math.max(0, activePill.offsetLeft - pillContainer.offsetLeft);
        }
    }

    // Escape Key to close modal sheet
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeNewComplaintSheet();
        }
    });

    // Form submit prevention double-click
    const quickForm = document.getElementById('formQuickComplaint');
    const submitBtn = document.getElementById('btnSubmitQuickComplaint');
    if (quickForm && submitBtn) {
        quickForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
        });
    }
});
</script>
@endsection
