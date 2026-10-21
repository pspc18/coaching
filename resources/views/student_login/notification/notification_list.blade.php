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

    if (!function_exists('cleanStudentCurrency')) {
        function cleanStudentCurrency($val) {
            if ($val === null || $val === '') return '0.00';
            $cleaned = preg_replace('/[^0-9.]/', '', (string) $val);
            if ($cleaned === '' || !is_numeric($cleaned)) return '0.00';
            return number_format((float) $cleaned, 2);
        }
    }

    if (!function_exists('parseStudentFeeNotificationContent')) {
        function parseStudentFeeNotificationContent($rawContent) {
            $text = trim((string) $rawContent);
            $parsed = [
                'student' => '',
                'receipt_no' => '',
                'payment_date' => '',
                'payment_mode' => '',
                'offline_receipt_no' => '',
                'fee_details' => '',
                'amount' => '',
                'discount' => '',
                'fine' => '',
                'total_paid' => '',
                'status' => 'Payment Received',
            ];

            if (preg_match('/Student:\s*([^\r\n]+)/i', $text, $m)) $parsed['student'] = trim($m[1]);
            if (preg_match('/Receipt No:\s*([^\r\n]+)/i', $text, $m)) $parsed['receipt_no'] = trim($m[1]);
            if (preg_match('/Payment Date:\s*([^\r\n]+)/i', $text, $m)) $parsed['payment_date'] = trim($m[1]);
            if (preg_match('/Payment Mode:\s*([^\r\n]+)/i', $text, $m)) $parsed['payment_mode'] = trim($m[1]);
            if (preg_match('/Offline Receipt No:\s*([^\r\n]+)/i', $text, $m)) $parsed['offline_receipt_no'] = trim($m[1]);
            if (preg_match('/Fee Details:\s*([\s\S]*?)(?=Amount:|Discount:|Fine:|Total Paid:|Status:|$)/i', $text, $m)) {
                $parsed['fee_details'] = trim(preg_replace('/\s+/', ' ', $m[1]));
            }
            if (preg_match('/Amount:\s*([^\r\n]+)/i', $text, $m)) $parsed['amount'] = trim($m[1]);
            if (preg_match('/Discount:\s*([^\r\n]+)/i', $text, $m)) $parsed['discount'] = trim($m[1]);
            if (preg_match('/Fine:\s*([^\r\n]+)/i', $text, $m)) $parsed['fine'] = trim($m[1]);
            if (preg_match('/Total Paid:\s*([^\r\n]+)/i', $text, $m)) $parsed['total_paid'] = trim($m[1]);
            if (preg_match('/Status:\s*([^\r\n]+)/i', $text, $m)) $parsed['status'] = trim($m[1]);

            if (empty($parsed['receipt_no']) && !empty($parsed['offline_receipt_no'])) {
                $parsed['receipt_no'] = $parsed['offline_receipt_no'];
            }

            return $parsed;
        }
    }

    if (!function_exists('parseStudentAttendanceNotificationContent')) {
        function parseStudentAttendanceNotificationContent($text) {
            $parsed = [
                'name' => '',
                'date' => '',
                'status' => 'Present',
                'check_in' => '',
                'check_out' => '',
                'institute' => '',
            ];
            if (preg_match('/Name:\s*([^\r\n]+)/i', $text, $m)) $parsed['name'] = trim($m[1]);
            if (preg_match('/Date:\s*([^\r\n]+)/i', $text, $m)) $parsed['date'] = trim($m[1]);
            if (preg_match('/Status:\s*([^\r\n]+)/i', $text, $m)) $parsed['status'] = trim($m[1]);
            if (preg_match('/Check-in:\s*([^\r\n]+)/i', $text, $m)) $parsed['check_in'] = trim($m[1]);
            if (preg_match('/Check-out:\s*([^\r\n]+)/i', $text, $m)) $parsed['check_out'] = trim($m[1]);
            if (preg_match('/(?:Status:[^\r\n]+\r?\n)([\s\S]+)$/i', $text, $m)) {
                $parsed['institute'] = trim(preg_replace('/\s+/', ' ', $m[1]));
            }
            return $parsed;
        }
    }

    if (!function_exists('parseStudentComplaintNotificationContent')) {
        function parseStudentComplaintNotificationContent($text) {
            $parsed = [
                'ticket' => '',
                'status' => 'Updated',
                'message' => '',
                'subject' => '',
            ];
            if (preg_match('/(CMP-[0-9A-Za-z_-]+)/i', $text, $m)) $parsed['ticket'] = trim($m[1]);
            if (preg_match('/Status:\s*([^\r\n]+)/i', $text, $m)) $parsed['status'] = trim($m[1]);
            elseif (preg_match('/status changed to\s*([^\r\n.]+)/i', $text, $m)) $parsed['status'] = trim($m[1]);
            if (preg_match('/Subject:\s*([^\r\n]+)/i', $text, $m)) $parsed['subject'] = trim($m[1]);
            if (preg_match('/Message:\s*([\s\S]+)$/i', $text, $m)) {
                $parsed['message'] = trim($m[1]);
            } else {
                $parsed['message'] = trim($text);
            }
            return $parsed;
        }
    }

    if (!function_exists('parseStudentExamNotificationContent')) {
        function parseStudentExamNotificationContent($text, $title = '') {
            $parsed = [
                'exam' => '',
                'message' => $text,
            ];
            if (preg_match('/result for\s+([^.]+?)\s+has been published/i', $text, $m)) {
                $parsed['exam'] = trim($m[1]);
            } elseif (preg_match('/Exam Result:\s*([^\r\n]+)/i', $text, $m)) {
                $parsed['exam'] = trim($m[1]);
            } else {
                $parsed['exam'] = trim(str_ireplace(['Exam Result Published', 'Exam Result'], '', (string) $title)) ?: 'Examination Result';
            }
            return $parsed;
        }
    }

    if (!function_exists('parseStudentFeeRevertNotificationContent')) {
        function parseStudentFeeRevertNotificationContent($text) {
            $parsed = [
                'student' => '',
                'receipt_no' => '',
                'date' => '',
                'mode' => '',
                'details' => '',
                'total_reverted' => '',
                'status' => 'Payment Reverted',
            ];
            if (preg_match('/Student:\s*([^\r\n]+)/i', $text, $m)) $parsed['student'] = trim($m[1]);
            if (preg_match('/Receipt No:\s*([^\r\n]+)/i', $text, $m)) $parsed['receipt_no'] = trim($m[1]);
            if (preg_match('/(?:Original Payment Date|Date):\s*([^\r\n]+)/i', $text, $m)) $parsed['date'] = trim($m[1]);
            if (preg_match('/Payment Mode:\s*([^\r\n]+)/i', $text, $m)) $parsed['mode'] = trim($m[1]);
            if (preg_match('/Reverted Fee Details:\s*([\s\S]*?)(?=Amount Reverted:|Discount Reversed:|Total Reverted:|Status:|$)/i', $text, $m)) {
                $parsed['details'] = trim(preg_replace('/\s+/', ' ', $m[1]));
            }
            if (preg_match('/Total Reverted:\s*([^\r\n]+)/i', $text, $m)) $parsed['total_reverted'] = trim($m[1]);
            elseif (preg_match('/Amount Reverted:\s*([^\r\n]+)/i', $text, $m)) $parsed['total_reverted'] = trim($m[1]);
            return $parsed;
        }
    }

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
    margin-bottom: 4px;
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
    line-height: 1.45;
    color: #334155;
    word-break: break-word;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 5px 8px;
    margin-top: 3px;
    margin-bottom: 5px;
    white-space: normal;
}

.mob-notif-body.collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: normal;
}

.mob-notif-body:not(.collapsed) {
    white-space: pre-line;
}

/* Generalized Structured Card Summary for All Categories */
.mob-structured-summary, .mob-fee-card-summary {
    border-radius: 4px;
    padding: 6px 8px;
    margin-top: 3px;
    margin-bottom: 5px;
    border: 1px solid transparent;
}
.mob-structured-summary.tone-green, .mob-fee-card-summary { background: #f0fdf4; border-color: #bbf7d0; }
.mob-structured-summary.tone-blue  { background: #f0f9ff; border-color: #bae6fd; }
.mob-structured-summary.tone-amber { background: #fffbeb; border-color: #fde68a; }
.mob-structured-summary.tone-purple{ background: #faf5ff; border-color: #e9d5ff; }
.mob-structured-summary.tone-cyan  { background: #ecfeff; border-color: #a5f3fc; }
.mob-structured-summary.tone-red   { background: #fff1f2; border-color: #fecdd3; }

.mob-summary-header, .mob-fee-summary-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 5px;
}
.mob-summary-tag, .mob-fee-receipt-tag {
    font-size: 9.5px;
    font-weight: 800;
    padding: 1.5px 6px;
    border-radius: 3px;
    letter-spacing: 0.3px;
    font-family: monospace;
    display: inline-flex;
    align-items: center;
    gap: 3.5px;
}
.tone-green .mob-summary-tag, .mob-fee-receipt-tag { color: #15803d; background: #dcfce7; border: 1px solid #86efac; }
.tone-blue  .mob-summary-tag { color: #0284c7; background: #e0f2fe; border: 1px solid #7dd3fc; }
.tone-amber .mob-summary-tag { color: #b45309; background: #fef3c7; border: 1px solid #fcd34d; }
.tone-purple .mob-summary-tag{ color: #7e22ce; background: #f3e8ff; border: 1px solid #d8b4fe; }
.tone-cyan  .mob-summary-tag { color: #0e7490; background: #cffafe; border: 1px solid #67e8f9; }
.tone-red   .mob-summary-tag { color: #be123c; background: #ffe4e6; border: 1px solid #fda4af; }

.mob-summary-status-badge, .mob-fee-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    background: #ffffff;
    padding: 1px 6px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-transform: uppercase;
    border: 1px solid transparent;
}
.tone-green .mob-summary-status-badge, .mob-fee-status-badge { color: #166534; border-color: #bbf7d0; }
.tone-blue  .mob-summary-status-badge { color: #0369a1; border-color: #bae6fd; }
.tone-amber .mob-summary-status-badge { color: #92400e; border-color: #fde68a; }
.tone-purple .mob-summary-status-badge{ color: #6b21a8; border-color: #e9d5ff; }
.tone-cyan  .mob-summary-status-badge { color: #155e75; border-color: #a5f3fc; }
.tone-red   .mob-summary-status-badge { color: #9f1239; border-color: #fecdd3; }

.mob-summary-hero, .mob-fee-amount-hero {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    background: #ffffff;
    border-radius: 3px;
    padding: 4px 8px;
    margin-bottom: 5px;
    border: 1px solid transparent;
}
.tone-green .mob-summary-hero, .mob-fee-amount-hero { border-color: #dcfce7; }
.tone-blue  .mob-summary-hero { border-color: #e0f2fe; }
.tone-amber .mob-summary-hero { border-color: #fef3c7; }
.tone-purple .mob-summary-hero{ border-color: #f3e8ff; }
.tone-cyan  .mob-summary-hero { border-color: #cffafe; }
.tone-red   .mob-summary-hero { border-color: #ffe4e6; }

.mob-summary-hero-lbl, .mob-fee-amount-lbl {
    font-size: 9.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.mob-summary-hero-val, .mob-fee-amount-val {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -0.3px;
}
.tone-green .mob-summary-hero-val, .mob-fee-amount-val { color: #15803d; }
.tone-blue  .mob-summary-hero-val { color: #0284c7; }
.tone-amber .mob-summary-hero-val { color: #b45309; }
.tone-purple .mob-summary-hero-val{ color: #7e22ce; }
.tone-cyan  .mob-summary-hero-val { color: #0e7490; }
.tone-red   .mob-summary-hero-val { color: #be123c; }

.mob-summary-grid, .mob-fee-mini-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px;
    font-size: 9px;
}
.mob-summary-item, .mob-fee-mini-item {
    background: rgba(255, 255, 255, 0.75);
    border-radius: 2px;
    padding: 2.5px 5px;
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    border: 1px solid transparent;
}
.tone-green .mob-summary-item, .mob-fee-mini-item { border-color: #dcfce7; }
.tone-blue  .mob-summary-item { border-color: #e0f2fe; }
.tone-amber .mob-summary-item { border-color: #fef3c7; }
.tone-purple .mob-summary-item{ border-color: #f3e8ff; }
.tone-cyan  .mob-summary-item { border-color: #cffafe; }
.tone-red   .mob-summary-item { border-color: #ffe4e6; }

.mob-summary-item.full, .mob-fee-mini-item.full {
    grid-column: span 2;
}
.mob-summary-item .lbl, .mob-fee-mini-item .lbl {
    color: #64748b;
    font-weight: 600;
    white-space: nowrap;
    font-size: 8.5px;
    flex-shrink: 0;
}
.mob-summary-item .val, .mob-fee-mini-item .val {
    color: #0f172a;
    font-weight: 700;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mob-summary-snippet {
    background: #ffffff;
    border-radius: 3px;
    padding: 4px 7px;
    font-size: 9.5px;
    line-height: 1.4;
    color: #334155;
    margin-top: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    border: 1px dashed #cbd5e1;
}

/* Action Link Pills on Cards */
.mob-card-action-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none !important;
    margin-bottom: 6px;
    border: 1px solid transparent;
    transition: all .15s ease;
}
.mob-card-action-pill.tone-green  { background: #ecfdf5; border-color: #86efac; color: #047857; }
.mob-card-action-pill.tone-green:hover  { background: #d1fae5; color: #065f46; }
.mob-card-action-pill.tone-blue   { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
.mob-card-action-pill.tone-blue:hover   { background: #dbeafe; color: #1e40af; }
.mob-card-action-pill.tone-amber  { background: #fffbeb; border-color: #fcd34d; color: #b45309; }
.mob-card-action-pill.tone-amber:hover  { background: #fef3c7; color: #92400e; }
.mob-card-action-pill.tone-purple { background: #faf5ff; border-color: #d8b4fe; color: #7e22ce; }
.mob-card-action-pill.tone-purple:hover { background: #f3e8ff; color: #6b21a8; }
.mob-card-action-pill.tone-cyan   { background: #ecfeff; border-color: #67e8f9; color: #0e7490; }
.mob-card-action-pill.tone-cyan:hover   { background: #cffafe; color: #155e75; }
.mob-card-action-pill.tone-red    { background: #fff1f2; border-color: #fda4af; color: #be123c; }
.mob-card-action-pill.tone-red:hover    { background: #ffe4e6; color: #9f1239; }
.mob-card-action-pill span {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mob-notif-attach.mob-notif-fee-receipt {
    background: #ecfdf5;
    border-color: #86efac;
    color: #047857;
    width: 100%;
    margin-bottom: 6px;
    justify-content: space-between;
}
.mob-notif-attach.mob-notif-fee-receipt:hover {
    background: #d1fae5;
    color: #065f46;
}
.mob-fee-dl-badge {
    font-size: 8.5px;
    font-weight: 800;
    background: #047857;
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
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

/* Modal Structured Cards for All Categories */
.sheet-structured-card, .sheet-fee-receipt-card {
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 10px;
    border: 1px solid transparent;
}
.sheet-structured-card.tone-green, .sheet-fee-receipt-card { background: #f0fdf4; border-color: #bbf7d0; }
.sheet-structured-card.tone-blue  { background: #f0f9ff; border-color: #bae6fd; }
.sheet-structured-card.tone-amber { background: #fffbeb; border-color: #fde68a; }
.sheet-structured-card.tone-purple{ background: #faf5ff; border-color: #e9d5ff; }
.sheet-structured-card.tone-cyan  { background: #ecfeff; border-color: #a5f3fc; }
.sheet-structured-card.tone-red   { background: #fff1f2; border-color: #fecdd3; }

.sheet-top-strip, .sheet-fee-top-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px dashed #cbd5e1;
}
.sheet-badge-tag, .sheet-fee-rec-badge {
    font-size: 10.5px;
    font-weight: 800;
    padding: 1.5px 7px;
    border-radius: 3px;
    letter-spacing: 0.3px;
    font-family: monospace;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.tone-green .sheet-badge-tag, .sheet-fee-rec-badge { color: #15803d; background: #dcfce7; border: 1px solid #86efac; }
.tone-blue  .sheet-badge-tag { color: #0284c7; background: #e0f2fe; border: 1px solid #7dd3fc; }
.tone-amber .sheet-badge-tag { color: #b45309; background: #fef3c7; border: 1px solid #fcd34d; }
.tone-purple .sheet-badge-tag{ color: #7e22ce; background: #f3e8ff; border: 1px solid #d8b4fe; }
.tone-cyan  .sheet-badge-tag { color: #0e7490; background: #cffafe; border: 1px solid #67e8f9; }
.tone-red   .sheet-badge-tag { color: #be123c; background: #ffe4e6; border: 1px solid #fda4af; }

.sheet-status-pill, .sheet-fee-status-badge {
    font-size: 9.5px;
    font-weight: 800;
    background: #ffffff;
    padding: 1.5px 7px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 3.5px;
    text-transform: uppercase;
    border: 1px solid transparent;
}
.tone-green .sheet-status-pill, .sheet-fee-status-badge { color: #166534; border-color: #86efac; }
.tone-blue  .sheet-status-pill { color: #0369a1; border-color: #7dd3fc; }
.tone-amber .sheet-status-pill { color: #92400e; border-color: #fcd34d; }
.tone-purple .sheet-status-pill{ color: #6b21a8; border-color: #d8b4fe; }
.tone-cyan  .sheet-status-pill { color: #155e75; border-color: #67e8f9; }
.tone-red   .sheet-status-pill { color: #9f1239; border-color: #fda4af; }

.sheet-hero-box, .sheet-fee-paid-hero {
    text-align: center;
    background: #ffffff;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 10px;
    border: 1px solid #e2e8f0;
}
.sheet-hero-box-lbl, .sheet-fee-hero-lbl {
    font-size: 9.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}
.sheet-hero-box-val, .sheet-fee-hero-amount {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -0.4px;
    line-height: 1.1;
}
.tone-green .sheet-hero-box-val, .sheet-fee-hero-amount { color: #15803d; }
.tone-blue  .sheet-hero-box-val { color: #0284c7; }
.tone-amber .sheet-hero-box-val { color: #b45309; }
.tone-purple .sheet-hero-box-val{ color: #7e22ce; }
.tone-cyan  .sheet-hero-box-val { color: #0e7490; }
.tone-red   .sheet-hero-box-val { color: #be123c; }

.sheet-breakdown-list, .sheet-fee-breakdown-list {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.sheet-row, .sheet-fee-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    font-size: 11px;
    padding: 4px 6px;
    background: rgba(255, 255, 255, 0.75);
    border-radius: 3px;
    border: 1px solid rgba(226, 232, 240, 0.4);
}
.sheet-row .row-lbl, .sheet-fee-row .row-lbl {
    color: #475569;
    font-weight: 600;
    flex-shrink: 0;
    font-size: 10.5px;
}
.sheet-row .row-val, .sheet-fee-row .row-val {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
    word-break: break-word;
    font-size: 11px;
}
.sheet-fee-row.highlight {
    background: #dcfce7;
    border: 1px solid #86efac;
}
.sheet-fee-row.highlight .row-lbl {
    color: #166534;
    font-weight: 800;
}
.sheet-fee-row.highlight .row-val {
    color: #15803d;
    font-weight: 900;
    font-size: 12px;
}
.sheet-msg-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 11px;
    line-height: 1.5;
    color: #1e293b;
    margin-bottom: 10px;
    white-space: pre-line;
}
.sheet-action-bar, .sheet-fee-action-bar {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}
.sheet-btn-primary, .sheet-fee-btn-primary {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none !important;
    transition: all .15s ease;
    border: 1px solid transparent;
}
.sheet-btn-primary.tone-green, .sheet-fee-btn-primary { background: #15803d; border-color: #166534; color: #ffffff !important; box-shadow: 0 2px 6px rgba(21, 128, 61, 0.25); }
.sheet-btn-primary.tone-blue  { background: #0284c7; border-color: #0369a1; color: #ffffff !important; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25); }
.sheet-btn-primary.tone-amber { background: #b45309; border-color: #92400e; color: #ffffff !important; box-shadow: 0 2px 6px rgba(180, 83, 9, 0.25); }
.sheet-btn-primary.tone-purple{ background: #7e22ce; border-color: #6b21a8; color: #ffffff !important; box-shadow: 0 2px 6px rgba(126, 34, 206, 0.25); }
.sheet-btn-primary.tone-cyan  { background: #0e7490; border-color: #155e75; color: #ffffff !important; box-shadow: 0 2px 6px rgba(14, 116, 144, 0.25); }
.sheet-btn-primary.tone-red   { background: #be123c; border-color: #9f1239; color: #ffffff !important; box-shadow: 0 2px 6px rgba(190, 18, 60, 0.25); }

.sheet-btn-secondary, .sheet-fee-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 8px 12px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54 !important;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none !important;
    transition: all .15s ease;
    flex-shrink: 0;
}
.sheet-btn-secondary:hover, .sheet-fee-btn-secondary:hover {
    background: #f1f5f9;
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
:root[data-theme="dark"] .mob-structured-summary.tone-green, :root[data-theme="dark"] .mob-fee-card-summary { background: #064e3b33; border-color: #065f46; }
:root[data-theme="dark"] .mob-structured-summary.tone-blue  { background: #0c4a6e33; border-color: #0284c7; }
:root[data-theme="dark"] .mob-structured-summary.tone-amber { background: #78350f33; border-color: #b45309; }
:root[data-theme="dark"] .mob-structured-summary.tone-purple{ background: #581c8733; border-color: #7e22ce; }
:root[data-theme="dark"] .mob-structured-summary.tone-cyan  { background: #164e6333; border-color: #0e7490; }
:root[data-theme="dark"] .mob-structured-summary.tone-red   { background: #88133733; border-color: #be123c; }

:root[data-theme="dark"] .mob-summary-hero, :root[data-theme="dark"] .mob-fee-amount-hero { background: #1e293b; }
:root[data-theme="dark"] .mob-summary-item, :root[data-theme="dark"] .mob-fee-mini-item { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-summary-item .val, :root[data-theme="dark"] .mob-fee-mini-item .val { color: #f1f5f9; }
:root[data-theme="dark"] .mob-summary-snippet { background: #1e293b; border-color: #334155; color: #cbd5e1; }

:root[data-theme="dark"] .sheet-structured-card, :root[data-theme="dark"] .sheet-fee-receipt-card { background: #0f172a; }
:root[data-theme="dark"] .sheet-hero-box, :root[data-theme="dark"] .sheet-fee-paid-hero { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .sheet-row, :root[data-theme="dark"] .sheet-fee-row { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .sheet-row .row-val, :root[data-theme="dark"] .sheet-fee-row .row-val { color: #f1f5f9; }
:root[data-theme="dark"] .sheet-msg-box { background: #1e293b; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .sheet-btn-secondary, :root[data-theme="dark"] .sheet-fee-btn-secondary { background: #1e293b; border-color: #334155; color: #f8fafc !important; }
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
                $rawContent = (string) ($notification->content ?? '');
                $cleanContent = trim(html_entity_decode(strip_tags($rawContent), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $cleanContent = preg_replace('/^[\s\r\n\xC2\xA0]+/u', '', $cleanContent);
                $cleanContent = preg_replace('/[\s\r\n\xC2\xA0]+$/u', '', $cleanContent);
                $content = $cleanContent;
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

                // Determine category / structured type
                $titleLower = strtolower((string) ($notification->title ?? ''));
                $contentLower = strtolower($content);

                $isFeePayment = ($notificationType === 'fee_payment' || stripos($titleLower, 'fee payment') !== false || stripos($content, 'Receipt No:') !== false);
                $isFeeRevert = ($notificationType === 'fee_revert' || stripos($titleLower, 'revert') !== false || stripos($content, 'Total Reverted:') !== false || stripos($content, 'Amount Reverted:') !== false);
                $isAttendance = ($notificationType === 'attendance' || stripos($notificationType, 'attendance') !== false || stripos($titleLower, 'attendance') !== false || stripos($content, 'Status: In') !== false || stripos($content, 'Status: Out') !== false || stripos($content, 'Status: Absent') !== false || stripos($content, 'Status: Present') !== false);
                $isExam = ($notificationType === 'exam_result' || stripos($titleLower, 'exam result') !== false || stripos($titleLower, 'result published') !== false || stripos($content, 'has been published') !== false);
                $isComplaint = (strpos($notificationType, 'complaint') !== false || stripos($titleLower, 'complaint') !== false || preg_match('/CMP-\d+/i', $content));
                $isNotice = ($notificationType === 'notice' || stripos($titleLower, 'notice') !== false || stripos($titleLower, 'circular') !== false);

                $structuredType = 'standard';
                if ($isFeePayment) {
                    $structuredType = 'fee_payment';
                    $config = ['icon' => 'fa-inr', 'label' => 'Payment', 'tone' => 'green', 'badge_class' => 'badge-soft-success'];
                } elseif ($isFeeRevert) {
                    $structuredType = 'fee_revert';
                    $config = ['icon' => 'fa-undo', 'label' => 'Fee Update', 'tone' => 'red', 'badge_class' => 'badge-soft-danger'];
                } elseif ($isAttendance) {
                    $structuredType = 'attendance';
                    $config = ['icon' => 'fa-calendar-check-o', 'label' => 'Attendance', 'tone' => 'blue', 'badge_class' => 'badge-soft-info'];
                } elseif ($isExam) {
                    $structuredType = 'exam_result';
                    $config = ['icon' => 'fa-file-text-o', 'label' => 'Result', 'tone' => 'purple', 'badge_class' => 'badge-soft-purple'];
                } elseif ($isComplaint) {
                    $structuredType = 'complaint';
                    $config = ['icon' => 'fa-comments-o', 'label' => 'Complaint', 'tone' => 'blue', 'badge_class' => 'badge-soft-slate'];
                } elseif ($isNotice) {
                    $structuredType = 'notice';
                    $config = ['icon' => 'fa-bullhorn', 'label' => 'Notice', 'tone' => 'amber', 'badge_class' => 'badge-soft-amber'];
                }

                $feeData = $isFeePayment ? parseStudentFeeNotificationContent($content) : null;
                $feeReceiptNo = $notification->fee_receipt_no ?? ($feeData['receipt_no'] ?? '');
                $feeReceiptDownloadUrl = $notification->fee_download_url ?? (!empty($feeReceiptNo) ? url('fees_receipt/'.$feeReceiptNo.'/download') : null);
                $feeReceiptViewUrl = $notification->fee_view_url ?? (!empty($feeReceiptNo) ? url('fees_receipt/'.$feeReceiptNo.'/view') : null);

                $revertData = $isFeeRevert ? parseStudentFeeRevertNotificationContent($content) : null;
                $attData = $isAttendance ? parseStudentAttendanceNotificationContent($content) : null;
                $examData = $isExam ? parseStudentExamNotificationContent($content, $notification->title ?? '') : null;
                $compData = $isComplaint ? parseStudentComplaintNotificationContent($content) : null;

                // Shared trigger data attributes
                $triggerAttrs = 'data-id="' . $notification->id . '" '
                    . 'data-title="' . e($notification->title ?: 'Notification') . '" '
                    . 'data-time="' . $createdAt->format('d M Y, h:i A') . '" '
                    . 'data-type="' . $config['label'] . '" '
                    . 'data-type-badge="' . $config['badge_class'] . '" '
                    . 'data-icon="' . $config['icon'] . '" '
                    . 'data-content="' . e($content) . '" '
                    . 'data-attachment="' . e($attachmentUrl) . '" '
                    . 'data-attachment-name="' . e($attachmentName) . '" '
                    . 'data-unread="' . ($isUnread ? '1' : '0') . '" '
                    . 'data-read-url="' . route('student.attendance-notifications.mark-read', $notification->id) . '" '
                    . 'data-structured-type="' . $structuredType . '" '
                    . 'data-is-fee="' . ($isFeePayment ? '1' : '0') . '"';

                if ($isFeePayment && $feeData) {
                    $triggerAttrs .= ' data-fee-student="' . e($feeData['student'] ?: $studentName) . '"'
                        . ' data-fee-receipt="' . e($feeReceiptNo) . '"'
                        . ' data-fee-offline-receipt="' . e($feeData['offline_receipt_no'] ?? '') . '"'
                        . ' data-fee-date="' . e($feeData['payment_date'] ?? '') . '"'
                        . ' data-fee-mode="' . e($feeData['payment_mode'] ?? '') . '"'
                        . ' data-fee-amount="' . e($feeData['amount'] ?? '') . '"'
                        . ' data-fee-discount="' . e($feeData['discount'] ?? '') . '"'
                        . ' data-fee-fine="' . e($feeData['fine'] ?? '') . '"'
                        . ' data-fee-total="' . e($feeData['total_paid'] ?: $feeData['amount']) . '"'
                        . ' data-fee-status="' . e($feeData['status'] ?? 'Payment Received') . '"'
                        . ' data-fee-details="' . e($feeData['fee_details'] ?? '') . '"'
                        . ' data-fee-download-url="' . e($feeReceiptDownloadUrl ?: '') . '"'
                        . ' data-fee-view-url="' . e($feeReceiptViewUrl ?: '') . '"';
                } elseif ($isAttendance && $attData) {
                    $triggerAttrs .= ' data-att-name="' . e($attData['name'] ?: $studentName) . '"'
                        . ' data-att-date="' . e($attData['date'] ?: $createdAt->format('d M Y')) . '"'
                        . ' data-att-status="' . e($attData['status'] ?: 'Present') . '"'
                        . ' data-att-check-in="' . e($attData['check_in'] ?? '') . '"'
                        . ' data-att-check-out="' . e($attData['check_out'] ?? '') . '"'
                        . ' data-att-institute="' . e($attData['institute'] ?? '') . '"'
                        . ' data-att-url="' . url('AttendanceView_student') . '"';
                } elseif ($isExam && $examData) {
                    $triggerAttrs .= ' data-exam-name="' . e($examData['exam'] ?: 'Examination') . '"'
                        . ' data-exam-student="' . e($studentName) . '"'
                        . ' data-exam-class="' . e($className) . '"'
                        . ' data-exam-date="' . $createdAt->format('d M Y') . '"'
                        . ' data-exam-msg="' . e($examData['message'] ?? $content) . '"'
                        . ' data-exam-url="' . url('/student/result-card') . '"';
                } elseif ($isComplaint && $compData) {
                    $triggerAttrs .= ' data-comp-ticket="' . e($compData['ticket'] ?: '') . '"'
                        . ' data-comp-status="' . e($compData['status'] ?: 'Updated') . '"'
                        . ' data-comp-subject="' . e($compData['subject'] ?? '') . '"'
                        . ' data-comp-date="' . $createdAt->format('d M Y') . '"'
                        . ' data-comp-msg="' . e($compData['message'] ?? $content) . '"'
                        . ' data-comp-url="' . url('student-complaints') . '"';
                } elseif ($isFeeRevert && $revertData) {
                    $triggerAttrs .= ' data-revert-student="' . e($revertData['student'] ?: $studentName) . '"'
                        . ' data-revert-receipt="' . e($revertData['receipt_no'] ?? '') . '"'
                        . ' data-revert-date="' . e($revertData['date'] ?: $createdAt->format('d M Y')) . '"'
                        . ' data-revert-mode="' . e($revertData['mode'] ?: 'Reversal') . '"'
                        . ' data-revert-total="' . e($revertData['total_reverted'] ?? '') . '"'
                        . ' data-revert-details="' . e($revertData['details'] ?? '') . '"'
                        . ' data-revert-url="' . url('fees_history') . '"';
                } elseif ($isNotice) {
                    $triggerAttrs .= ' data-notice-title="' . e($notification->title ?: 'Notice') . '"'
                        . ' data-notice-date="' . $createdAt->format('d M Y') . '"'
                        . ' data-notice-msg="' . e($content) . '"'
                        . ' data-notice-url="' . url('student-notices') . '"';
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
                                onclick="openNotifSheet(this); return false;"
                                {!! $triggerAttrs !!}
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
                @if($structuredType === 'fee_payment' && !empty($feeData) && (!empty($feeData['total_paid']) || !empty($feeData['receipt_no'])))
                    <div class="mob-fee-card-summary">
                        <div class="mob-fee-summary-header">
                            @if(!empty($feeReceiptNo))
                                <span class="mob-fee-receipt-tag">
                                    <i class="fa fa-file-text-o"></i> REC #{{ $feeReceiptNo }}
                                </span>
                            @endif
                            <span class="mob-fee-status-badge">
                                <i class="fa fa-check-circle text-success"></i> {{ $feeData['status'] ?: 'Payment Received' }}
                            </span>
                        </div>

                        <div class="mob-fee-amount-hero">
                            <span class="mob-fee-amount-lbl">Total Paid</span>
                            <span class="mob-fee-amount-val">
                                <i class="fa fa-inr"></i> {{ cleanStudentCurrency($feeData['total_paid'] ?: $feeData['amount']) }}
                            </span>
                        </div>

                        <div class="mob-fee-mini-grid">
                            @if(!empty($feeData['payment_date']))
                                <div class="mob-fee-mini-item">
                                    <span class="lbl"><i class="fa fa-calendar-o"></i> Date</span>
                                    <span class="val">{{ $feeData['payment_date'] }}</span>
                                </div>
                            @endif
                            @if(!empty($feeData['payment_mode']))
                                <div class="mob-fee-mini-item">
                                    <span class="lbl"><i class="fa fa-credit-card"></i> Mode</span>
                                    <span class="val">{{ $feeData['payment_mode'] }}</span>
                                </div>
                            @endif
                            @if(!empty($feeData['fee_details']))
                                <div class="mob-fee-mini-item" style="grid-column: span 2;">
                                    <span class="lbl"><i class="fa fa-bookmark-o"></i> Fee Head</span>
                                    <span class="val">{{ $feeData['fee_details'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!empty($feeReceiptDownloadUrl))
                        <a href="{{ $feeReceiptDownloadUrl }}"
                           class="mob-notif-attach mob-notif-fee-receipt"
                           target="_blank"
                           title="Download Real-Time Fee Receipt PDF">
                            <i class="fa fa-file-pdf-o text-danger"></i>
                            <span>Download Receipt PDF{{ !empty($feeReceiptNo) ? ' (#'.$feeReceiptNo.')' : '' }}</span>
                            <span class="mob-fee-dl-badge ms-auto"><i class="fa fa-download"></i> PDF</span>
                        </a>
                    @endif

                @elseif($structuredType === 'attendance' && !empty($attData))
                    <div class="mob-structured-summary tone-blue">
                        <div class="mob-summary-header">
                            <span class="mob-summary-tag">
                                <i class="fa fa-calendar-check-o"></i> {{ $attData['date'] ?: $createdAt->format('d M Y') }}
                            </span>
                            <span class="mob-summary-status-badge">
                                @php $stLower = strtolower($attData['status'] ?? ''); @endphp
                                @if(in_array($stLower, ['present', 'in', 'p']))
                                    <i class="fa fa-check-circle text-success"></i> {{ $attData['status'] }}
                                @elseif(in_array($stLower, ['absent', 'a']))
                                    <i class="fa fa-times-circle text-danger"></i> Absent
                                @elseif(in_array($stLower, ['out']))
                                    <i class="fa fa-sign-out text-info"></i> Out
                                @else
                                    <i class="fa fa-info-circle text-primary"></i> {{ $attData['status'] ?: 'Present' }}
                                @endif
                            </span>
                        </div>

                        <div class="mob-summary-hero">
                            <span class="mob-summary-hero-lbl">Attendance Status</span>
                            <span class="mob-summary-hero-val">
                                {{ strtoupper($attData['status'] ?: 'PRESENT') }}
                            </span>
                        </div>

                        <div class="mob-summary-grid">
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-user-o"></i> Student</span>
                                <span class="val">{{ $attData['name'] ?: $studentName }}</span>
                            </div>
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-calendar-o"></i> Date</span>
                                <span class="val">{{ $attData['date'] ?: $createdAt->format('d M Y') }}</span>
                            </div>
                            @if(!empty($attData['check_in']))
                                <div class="mob-summary-item">
                                    <span class="lbl"><i class="fa fa-sign-in"></i> In Time</span>
                                    <span class="val">{{ $attData['check_in'] }}</span>
                                </div>
                            @endif
                            @if(!empty($attData['check_out']))
                                <div class="mob-summary-item">
                                    <span class="lbl"><i class="fa fa-sign-out"></i> Out Time</span>
                                    <span class="val">{{ $attData['check_out'] }}</span>
                                </div>
                            @endif
                            @if(!empty($attData['institute']))
                                <div class="mob-summary-item full">
                                    <span class="lbl"><i class="fa fa-building-o"></i> Campus</span>
                                    <span class="val">{{ $attData['institute'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ url('AttendanceView_student') }}" class="mob-card-action-pill tone-blue" title="View Attendance Register">
                        <i class="fa fa-calendar-check-o"></i>
                        <span>View Attendance Register</span>
                        <i class="fa fa-arrow-right ms-auto"></i>
                    </a>

                @elseif($structuredType === 'exam_result' && !empty($examData))
                    <div class="mob-structured-summary tone-purple">
                        <div class="mob-summary-header">
                            <span class="mob-summary-tag">
                                <i class="fa fa-trophy"></i> EXAM RESULT
                            </span>
                            <span class="mob-summary-status-badge">
                                <i class="fa fa-check-circle" style="color:#7e22ce;"></i> Published
                            </span>
                        </div>

                        <div class="mob-summary-hero">
                            <span class="mob-summary-hero-lbl">Examination</span>
                            <span class="mob-summary-hero-val" style="font-size:12px; font-weight:800;">
                                {{ $examData['exam'] ?: 'Exam Result' }}
                            </span>
                        </div>

                        <div class="mob-summary-grid">
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-graduation-cap"></i> Class</span>
                                <span class="val">{{ $className }}</span>
                            </div>
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-calendar-o"></i> Declared</span>
                                <span class="val">{{ $createdAt->format('d M Y') }}</span>
                            </div>
                        </div>

                        @if(!empty($examData['message']))
                            <div class="mob-summary-snippet">
                                {{ $examData['message'] }}
                            </div>
                        @endif
                    </div>

                    <a href="{{ url('/student/result-card') }}" class="mob-card-action-pill tone-purple" title="View & Download Result Card">
                        <i class="fa fa-file-text-o"></i>
                        <span>View & Download Result Card</span>
                        <i class="fa fa-arrow-right ms-auto"></i>
                    </a>

                @elseif($structuredType === 'complaint' && !empty($compData))
                    <div class="mob-structured-summary tone-blue">
                        <div class="mob-summary-header">
                            <span class="mob-summary-tag">
                                <i class="fa fa-ticket"></i> {{ $compData['ticket'] ?: 'SUPPORT' }}
                            </span>
                            <span class="mob-summary-status-badge">
                                <i class="fa fa-circle text-primary" style="font-size:7px;"></i> {{ $compData['status'] ?: 'Updated' }}
                            </span>
                        </div>

                        <div class="mob-summary-hero">
                            <span class="mob-summary-hero-lbl">Ticket Status</span>
                            <span class="mob-summary-hero-val">
                                {{ strtoupper($compData['status'] ?: 'UPDATED') }}
                            </span>
                        </div>

                        <div class="mob-summary-grid">
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-hashtag"></i> Ticket ID</span>
                                <span class="val">{{ $compData['ticket'] ?: 'Helpdesk' }}</span>
                            </div>
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-calendar-o"></i> Date</span>
                                <span class="val">{{ $createdAt->format('d M Y') }}</span>
                            </div>
                        </div>

                        @if(!empty($compData['message']))
                            <div class="mob-summary-snippet">
                                {{ $compData['message'] }}
                            </div>
                        @endif
                    </div>

                    <a href="{{ url('student-complaints') }}" class="mob-card-action-pill tone-blue" title="Open Complaint Desk">
                        <i class="fa fa-comments-o"></i>
                        <span>View Complaint Discussion</span>
                        <i class="fa fa-arrow-right ms-auto"></i>
                    </a>

                @elseif($structuredType === 'fee_revert' && !empty($revertData))
                    <div class="mob-structured-summary tone-red">
                        <div class="mob-summary-header">
                            <span class="mob-summary-tag">
                                <i class="fa fa-undo"></i> {{ !empty($revertData['receipt_no']) ? 'REC #'.$revertData['receipt_no'] : 'REVERSAL' }}
                            </span>
                            <span class="mob-summary-status-badge">
                                <i class="fa fa-exclamation-circle text-danger"></i> Reverted
                            </span>
                        </div>

                        <div class="mob-summary-hero">
                            <span class="mob-summary-hero-lbl">Total Reverted</span>
                            <span class="mob-summary-hero-val">
                                <i class="fa fa-inr"></i> {{ cleanStudentCurrency($revertData['total_reverted']) }}
                            </span>
                        </div>

                        <div class="mob-summary-grid">
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-calendar-o"></i> Date</span>
                                <span class="val">{{ $revertData['date'] ?: $createdAt->format('d M Y') }}</span>
                            </div>
                            <div class="mob-summary-item">
                                <span class="lbl"><i class="fa fa-credit-card"></i> Mode</span>
                                <span class="val">{{ $revertData['mode'] ?: 'Reversal' }}</span>
                            </div>
                            @if(!empty($revertData['details']))
                                <div class="mob-summary-item full">
                                    <span class="lbl"><i class="fa fa-bookmark-o"></i> Details</span>
                                    <span class="val">{{ $revertData['details'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ url('fees_history') }}" class="mob-card-action-pill tone-red" title="View Fee History">
                        <i class="fa fa-history"></i>
                        <span>View Fees Transaction History</span>
                        <i class="fa fa-arrow-right ms-auto"></i>
                    </a>

                @elseif($structuredType === 'notice')
                    <div class="mob-structured-summary tone-amber">
                        <div class="mob-summary-header">
                            <span class="mob-summary-tag">
                                <i class="fa fa-bullhorn"></i> NOTICE
                            </span>
                            <span class="mob-summary-status-badge">
                                <i class="fa fa-clock-o text-warning"></i> {{ $createdAt->format('d M Y') }}
                            </span>
                        </div>

                        <div class="mob-summary-hero">
                            <span class="mob-summary-hero-lbl">Circular Topic</span>
                            <span class="mob-summary-hero-val" style="font-size:12px; font-weight:800;">
                                {{ $notification->title ?: 'Notice Announcement' }}
                            </span>
                        </div>

                        <div class="mob-summary-snippet">
                            {{ $content !== '' ? $content : 'Circular update published by administration.' }}
                        </div>
                    </div>

                    @if(!empty($attachmentUrl))
                        <a href="{{ $attachmentUrl }}" class="mob-card-action-pill tone-amber" target="_blank" download title="Download Notice Attachment">
                            <i class="fa {{ preg_match('/\.(jpg|jpeg|png|webp)$/i', $attachmentUrl) ? 'fa-file-image-o' : 'fa-file-pdf-o' }}"></i>
                            <span>{{ $attachmentName ?: 'Download Notice Attachment' }}</span>
                            <i class="fa fa-download ms-auto"></i>
                        </a>
                    @endif

                    <a href="{{ url('student-notices') }}" class="mob-card-action-pill tone-amber" title="Open Notice Board">
                        <i class="fa fa-bullhorn"></i>
                        <span>Open School Notice Board</span>
                        <i class="fa fa-arrow-right ms-auto"></i>
                    </a>

                @else
                    {{-- Standard fallback --}}
                    <div class="mob-notif-body {{ $isLong ? 'collapsed' : '' }}" id="notifBody-{{ $notification->id }}">{{ $content !== '' ? $content : 'No message content available.' }}</div>

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
                @endif

                {{-- Card Footer & Actions --}}
                <div class="mob-notif-footer">
                    <span>
                        <i class="fa fa-calendar-o"></i> {{ $createdAt->format('d M Y, h:i A') }}
                    </span>

                    <div class="mob-notif-actions">
                        @if($isLong && $structuredType === 'standard')
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
                                onclick="openNotifSheet(this); return false;"
                                {!! $triggerAttrs !!}>
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

        {{-- 1. Fee Payment Structured Section --}}
        <div id="sheetFeeContainer" style="display: none;">
            <div class="sheet-fee-receipt-card tone-green">
                <div class="sheet-fee-top-strip">
                    <span class="sheet-fee-rec-badge" id="sheetFeeReceiptTag"><i class="fa fa-file-text-o"></i> REC #-</span>
                    <span class="sheet-fee-status-badge" id="sheetFeeStatusTag"><i class="fa fa-check-circle text-success"></i> Payment Received</span>
                </div>

                <div class="sheet-fee-paid-hero">
                    <div class="sheet-fee-hero-lbl">Total Amount Paid</div>
                    <div class="sheet-fee-hero-amount" id="sheetFeePaidAmount"><i class="fa fa-inr"></i> 0.00</div>
                </div>

                <div class="sheet-fee-breakdown-list">
                    <div class="sheet-fee-row" id="sheetFeeRowStudent">
                        <span class="row-lbl">Student Name</span>
                        <span class="row-val" id="sheetFeeStudentVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowDate">
                        <span class="row-lbl">Payment Date</span>
                        <span class="row-val" id="sheetFeeDateVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowMode">
                        <span class="row-lbl">Payment Mode</span>
                        <span class="row-val" id="sheetFeeModeVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowDetails">
                        <span class="row-lbl">Fee Head / Remarks</span>
                        <span class="row-val" id="sheetFeeDetailsVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowBase">
                        <span class="row-lbl">Amount</span>
                        <span class="row-val" id="sheetFeeBaseVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowDiscount" style="display:none;">
                        <span class="row-lbl">Discount</span>
                        <span class="row-val" id="sheetFeeDiscountVal">-</span>
                    </div>
                    <div class="sheet-fee-row" id="sheetFeeRowFine" style="display:none;">
                        <span class="row-lbl">Fine / Late Fee</span>
                        <span class="row-val" id="sheetFeeFineVal">-</span>
                    </div>
                    <div class="sheet-fee-row highlight">
                        <span class="row-lbl">Net Total Paid</span>
                        <span class="row-val" id="sheetFeeNetVal">-</span>
                    </div>
                </div>

                <div class="sheet-fee-action-bar">
                    <a href="#" id="sheetFeeDownloadBtn" class="sheet-fee-btn-primary" target="_blank">
                        <i class="fa fa-file-pdf-o"></i> Download Receipt PDF
                    </a>
                    <a href="#" id="sheetFeeViewBtn" class="sheet-fee-btn-secondary" target="_blank">
                        <i class="fa fa-external-link"></i> View Receipt
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. Attendance Structured Section --}}
        <div id="sheetAttendanceContainer" style="display: none;">
            <div class="sheet-structured-card tone-blue">
                <div class="sheet-top-strip">
                    <span class="sheet-badge-tag" id="sheetAttBadgeTag"><i class="fa fa-calendar-check-o"></i> Attendance</span>
                    <span class="sheet-status-pill" id="sheetAttStatusBadge"><i class="fa fa-check-circle text-success"></i> Present</span>
                </div>

                <div class="sheet-hero-box">
                    <div class="sheet-hero-box-lbl">ATTENDANCE STATUS</div>
                    <div class="sheet-hero-box-val" id="sheetAttHeroVal">PRESENT</div>
                </div>

                <div class="sheet-breakdown-list">
                    <div class="sheet-row">
                        <span class="row-lbl">Student Name</span>
                        <span class="row-val" id="sheetAttStudentVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Attendance Date</span>
                        <span class="row-val" id="sheetAttDateVal">-</span>
                    </div>
                    <div class="sheet-row" id="sheetAttRowCheckIn" style="display:none;">
                        <span class="row-lbl">Check-in Time</span>
                        <span class="row-val" id="sheetAttCheckInVal">-</span>
                    </div>
                    <div class="sheet-row" id="sheetAttRowCheckOut" style="display:none;">
                        <span class="row-lbl">Check-out Time</span>
                        <span class="row-val" id="sheetAttCheckOutVal">-</span>
                    </div>
                    <div class="sheet-row" id="sheetAttRowInstitute" style="display:none;">
                        <span class="row-lbl">Campus / Branch</span>
                        <span class="row-val" id="sheetAttInstituteVal">-</span>
                    </div>
                </div>

                <div class="sheet-action-bar">
                    <a href="{{ url('AttendanceView_student') }}" id="sheetAttLinkBtn" class="sheet-btn-primary tone-blue">
                        <i class="fa fa-calendar-check-o"></i> View Attendance Register
                    </a>
                </div>
            </div>
        </div>

        {{-- 3. Exam Result Structured Section --}}
        <div id="sheetExamContainer" style="display: none;">
            <div class="sheet-structured-card tone-purple">
                <div class="sheet-top-strip">
                    <span class="sheet-badge-tag"><i class="fa fa-trophy"></i> EXAM RESULT</span>
                    <span class="sheet-status-pill" style="color:#6b21a8; border-color:#d8b4fe;"><i class="fa fa-check-circle"></i> Published</span>
                </div>

                <div class="sheet-hero-box">
                    <div class="sheet-hero-box-lbl">EXAMINATION</div>
                    <div class="sheet-hero-box-val" id="sheetExamHeroVal" style="font-size:15px;">-</div>
                </div>

                <div class="sheet-breakdown-list" style="margin-bottom:8px;">
                    <div class="sheet-row">
                        <span class="row-lbl">Student Name</span>
                        <span class="row-val" id="sheetExamStudentVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Class / Course</span>
                        <span class="row-val" id="sheetExamClassVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Declaration Date</span>
                        <span class="row-val" id="sheetExamDateVal">-</span>
                    </div>
                </div>

                <div class="sheet-msg-box" id="sheetExamMsgVal"></div>

                <div class="sheet-action-bar">
                    <a href="{{ url('/student/result-card') }}" id="sheetExamLinkBtn" class="sheet-btn-primary tone-purple">
                        <i class="fa fa-trophy"></i> Open Result Card
                    </a>
                </div>
            </div>
        </div>

        {{-- 4. Complaint / Support Structured Section --}}
        <div id="sheetComplaintContainer" style="display: none;">
            <div class="sheet-structured-card tone-blue">
                <div class="sheet-top-strip">
                    <span class="sheet-badge-tag" id="sheetCompTicketTag"><i class="fa fa-ticket"></i> TICKET</span>
                    <span class="sheet-status-pill" id="sheetCompStatusTag"><i class="fa fa-circle text-primary" style="font-size:7px;"></i> Open</span>
                </div>

                <div class="sheet-hero-box">
                    <div class="sheet-hero-box-lbl">TICKET STATUS</div>
                    <div class="sheet-hero-box-val" id="sheetCompHeroVal">-</div>
                </div>

                <div class="sheet-breakdown-list" style="margin-bottom:8px;">
                    <div class="sheet-row">
                        <span class="row-lbl">Ticket Number</span>
                        <span class="row-val" id="sheetCompTicketVal">-</span>
                    </div>
                    <div class="sheet-row" id="sheetCompRowSubject" style="display:none;">
                        <span class="row-lbl">Subject</span>
                        <span class="row-val" id="sheetCompSubjectVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Updated On</span>
                        <span class="row-val" id="sheetCompDateVal">-</span>
                    </div>
                </div>

                <div class="sheet-msg-box" id="sheetCompMsgVal"></div>

                <div class="sheet-action-bar">
                    <a href="{{ url('student-complaints') }}" id="sheetCompLinkBtn" class="sheet-btn-primary tone-blue">
                        <i class="fa fa-comments-o"></i> Go to Complaints Desk
                    </a>
                </div>
            </div>
        </div>

        {{-- 5. Fee Revert Structured Section --}}
        <div id="sheetFeeRevertContainer" style="display: none;">
            <div class="sheet-structured-card tone-red">
                <div class="sheet-top-strip">
                    <span class="sheet-badge-tag" id="sheetRevertReceiptTag"><i class="fa fa-undo"></i> REC #-</span>
                    <span class="sheet-status-pill" style="color:#9f1239; border-color:#fda4af;"><i class="fa fa-exclamation-circle"></i> Reverted</span>
                </div>

                <div class="sheet-hero-box">
                    <div class="sheet-hero-box-lbl">TOTAL AMOUNT REVERTED</div>
                    <div class="sheet-hero-box-val" id="sheetRevertHeroVal"><i class="fa fa-inr"></i> 0.00</div>
                </div>

                <div class="sheet-breakdown-list">
                    <div class="sheet-row">
                        <span class="row-lbl">Student Name</span>
                        <span class="row-val" id="sheetRevertStudentVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Reversal Date</span>
                        <span class="row-val" id="sheetRevertDateVal">-</span>
                    </div>
                    <div class="sheet-row">
                        <span class="row-lbl">Payment Mode</span>
                        <span class="row-val" id="sheetRevertModeVal">-</span>
                    </div>
                    <div class="sheet-row" id="sheetRevertRowDetails" style="display:none;">
                        <span class="row-lbl">Reverted Details</span>
                        <span class="row-val" id="sheetRevertDetailsVal">-</span>
                    </div>
                </div>

                <div class="sheet-action-bar">
                    <a href="{{ url('fees_history') }}" id="sheetRevertLinkBtn" class="sheet-btn-primary tone-red">
                        <i class="fa fa-history"></i> View Fees History
                    </a>
                </div>
            </div>
        </div>

        {{-- 6. Notice / Circular Structured Section --}}
        <div id="sheetNoticeContainer" style="display: none;">
            <div class="sheet-structured-card tone-amber">
                <div class="sheet-top-strip">
                    <span class="sheet-badge-tag"><i class="fa fa-bullhorn"></i> NOTICE</span>
                    <span class="sheet-status-pill" id="sheetNoticeDateTag"><i class="fa fa-clock-o"></i> Date</span>
                </div>

                <div class="sheet-hero-box">
                    <div class="sheet-hero-box-lbl">CIRCULAR TOPIC</div>
                    <div class="sheet-hero-box-val" id="sheetNoticeHeroVal" style="font-size:14px;">-</div>
                </div>

                <div class="sheet-msg-box" id="sheetNoticeMsgVal"></div>

                <div class="sheet-action-bar" style="flex-direction: column;">
                    <a href="#" id="sheetNoticeAttachBtn" class="sheet-btn-secondary" target="_blank" download style="display:none; justify-content:center;">
                        <i class="fa fa-paperclip"></i> Download Attachment
                    </a>
                    <a href="{{ url('student-notices') }}" id="sheetNoticeLinkBtn" class="sheet-btn-primary tone-amber">
                        <i class="fa fa-bullhorn"></i> Open School Notice Board
                    </a>
                </div>
            </div>
        </div>

        {{-- Standard Message Body (for general alerts / notices) --}}
        <div id="sheetStandardContainer">
            <div class="sheet-notif-card">
                <div id="sheetNotifBody" class="sheet-notif-text"></div>
            </div>
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
    try {
        const id = triggerEl.getAttribute('data-id');
        const title = triggerEl.getAttribute('data-title') || 'Notification';
        const time = triggerEl.getAttribute('data-time') || '-';
        const type = triggerEl.getAttribute('data-type') || 'Notification';
        const typeBadge = triggerEl.getAttribute('data-type-badge') || 'badge-soft-info';
        const iconClass = triggerEl.getAttribute('data-icon') || 'fa-bell-o';
        const content = triggerEl.getAttribute('data-content') || '';
        const attachment = triggerEl.getAttribute('data-attachment') || '';
        const attachmentName = triggerEl.getAttribute('data-attachment-name') || '';
        const isUnread = triggerEl.getAttribute('data-unread') === '1';
        const readUrl = triggerEl.getAttribute('data-read-url');
        const structuredType = triggerEl.getAttribute('data-structured-type') || (triggerEl.getAttribute('data-is-fee') === '1' ? 'fee_payment' : 'standard');

        // Fee attributes
        const isFee = triggerEl.getAttribute('data-is-fee') === '1';
        const feeReceipt = triggerEl.getAttribute('data-fee-receipt') || '';
        const feeStudent = triggerEl.getAttribute('data-fee-student') || '';
        const feeDate = triggerEl.getAttribute('data-fee-date') || '';
        const feeMode = triggerEl.getAttribute('data-fee-mode') || '';
        const feeDetails = triggerEl.getAttribute('data-fee-details') || '';
        const feeAmount = triggerEl.getAttribute('data-fee-amount') || '';
        const feeDiscount = triggerEl.getAttribute('data-fee-discount') || '';
        const feeFine = triggerEl.getAttribute('data-fee-fine') || '';
        const feeTotal = triggerEl.getAttribute('data-fee-total') || '';
        const feeStatus = triggerEl.getAttribute('data-fee-status') || 'Payment Received';
        const feeDownloadUrl = triggerEl.getAttribute('data-fee-download-url') || '';
        const feeViewUrl = triggerEl.getAttribute('data-fee-view-url') || '';

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

        // Populate title
        const titleEl = document.getElementById('sheetNotifTitle');
        if (titleEl) titleEl.textContent = title;

        // Currency formatter
        const formatCurrency = function(val) {
            if (!val && val !== 0) return '0.00';
            const clean = String(val).replace(/[^0-9.-]+/g, '');
            const num = parseFloat(clean);
            if (isNaN(num)) return val;
            return num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const containers = {
            'fee_payment': document.getElementById('sheetFeeContainer'),
            'attendance': document.getElementById('sheetAttendanceContainer'),
            'exam_result': document.getElementById('sheetExamContainer'),
            'complaint': document.getElementById('sheetComplaintContainer'),
            'fee_revert': document.getElementById('sheetFeeRevertContainer'),
            'notice': document.getElementById('sheetNoticeContainer'),
            'standard': document.getElementById('sheetStandardContainer')
        };
        const attachWrap = document.getElementById('sheetNotifAttachment');

        // Hide all containers initially
        Object.keys(containers).forEach(function(k) {
            if (containers[k]) containers[k].style.display = 'none';
        });

        if (structuredType === 'fee_payment') {
            const feeContainer = containers['fee_payment'];
            if (feeContainer) {
                feeContainer.style.display = 'block';

                const feeReceiptTag = document.getElementById('sheetFeeReceiptTag');
                if (feeReceiptTag) {
                    feeReceiptTag.innerHTML = feeReceipt ? '<i class="fa fa-file-text-o"></i> REC #' + feeReceipt : '<i class="fa fa-file-text-o"></i> Fee Receipt';
                }

                const feeStatusTag = document.getElementById('sheetFeeStatusTag');
                if (feeStatusTag) {
                    feeStatusTag.innerHTML = '<i class="fa fa-check-circle text-success"></i> ' + (feeStatus || 'Payment Received');
                }

                const paidAmountEl = document.getElementById('sheetFeePaidAmount');
                if (paidAmountEl) {
                    paidAmountEl.innerHTML = '<i class="fa fa-inr"></i> ' + formatCurrency(feeTotal || feeAmount);
                }

                const studentEl = document.getElementById('sheetFeeStudentVal');
                if (studentEl) studentEl.textContent = feeStudent || '-';

                const dateEl = document.getElementById('sheetFeeDateVal');
                if (dateEl) dateEl.textContent = feeDate || time;

                const modeEl = document.getElementById('sheetFeeModeVal');
                if (modeEl) modeEl.textContent = feeMode || 'Cash';

                const detailsEl = document.getElementById('sheetFeeDetailsVal');
                if (detailsEl) detailsEl.textContent = feeDetails || 'School Fees';

                const baseEl = document.getElementById('sheetFeeBaseVal');
                if (baseEl) baseEl.textContent = '₹ ' + formatCurrency(feeAmount || feeTotal);

                const rowDiscount = document.getElementById('sheetFeeRowDiscount');
                const discountEl = document.getElementById('sheetFeeDiscountVal');
                const discNum = parseFloat(String(feeDiscount).replace(/[^0-9.-]+/g, '')) || 0;
                if (rowDiscount && discountEl) {
                    if (discNum > 0) {
                        rowDiscount.style.display = 'flex';
                        discountEl.textContent = '- ₹ ' + formatCurrency(feeDiscount);
                    } else {
                        rowDiscount.style.display = 'none';
                    }
                }

                const rowFine = document.getElementById('sheetFeeRowFine');
                const fineEl = document.getElementById('sheetFeeFineVal');
                const fineNum = parseFloat(String(feeFine).replace(/[^0-9.-]+/g, '')) || 0;
                if (rowFine && fineEl) {
                    if (fineNum > 0) {
                        rowFine.style.display = 'flex';
                        fineEl.textContent = '+ ₹ ' + formatCurrency(feeFine);
                    } else {
                        rowFine.style.display = 'none';
                    }
                }

                const netEl = document.getElementById('sheetFeeNetVal');
                if (netEl) netEl.textContent = '₹ ' + formatCurrency(feeTotal || feeAmount);

                // Action buttons
                const downloadBtn = document.getElementById('sheetFeeDownloadBtn');
                if (downloadBtn) {
                    if (feeDownloadUrl) {
                        downloadBtn.href = feeDownloadUrl;
                        downloadBtn.style.display = 'inline-flex';
                    } else {
                        downloadBtn.style.display = 'none';
                    }
                }

                const viewBtn = document.getElementById('sheetFeeViewBtn');
                if (viewBtn) {
                    if (feeViewUrl) {
                        viewBtn.href = feeViewUrl;
                        viewBtn.style.display = 'inline-flex';
                    } else {
                        viewBtn.style.display = 'none';
                    }
                }
            }
        } else if (structuredType === 'attendance') {
            const attContainer = containers['attendance'];
            if (attContainer) {
                attContainer.style.display = 'block';

                const attName = triggerEl.getAttribute('data-att-name') || '-';
                const attDate = triggerEl.getAttribute('data-att-date') || time;
                const attStatus = (triggerEl.getAttribute('data-att-status') || 'Present').trim();
                const attCheckIn = triggerEl.getAttribute('data-att-check-in') || '';
                const attCheckOut = triggerEl.getAttribute('data-att-check-out') || '';
                const attInstitute = triggerEl.getAttribute('data-att-institute') || '';
                const attUrl = triggerEl.getAttribute('data-att-url') || '#';

                const badgeTag = document.getElementById('sheetAttBadgeTag');
                if (badgeTag) badgeTag.innerHTML = '<i class="fa fa-calendar-check-o"></i> ' + attDate;

                const statusBadge = document.getElementById('sheetAttStatusBadge');
                const stLower = attStatus.toLowerCase();
                let iconHtml = '<i class="fa fa-info-circle text-primary"></i> ';
                if (['present', 'in', 'p'].includes(stLower)) {
                    iconHtml = '<i class="fa fa-check-circle text-success"></i> ';
                } else if (['absent', 'a'].includes(stLower)) {
                    iconHtml = '<i class="fa fa-times-circle text-danger"></i> ';
                } else if (['out'].includes(stLower)) {
                    iconHtml = '<i class="fa fa-sign-out text-info"></i> ';
                }
                if (statusBadge) statusBadge.innerHTML = iconHtml + attStatus;

                const heroVal = document.getElementById('sheetAttHeroVal');
                if (heroVal) heroVal.textContent = attStatus.toUpperCase();

                const studentEl = document.getElementById('sheetAttStudentVal');
                if (studentEl) studentEl.textContent = attName;

                const dateEl = document.getElementById('sheetAttDateVal');
                if (dateEl) dateEl.textContent = attDate;

                const rowIn = document.getElementById('sheetAttRowCheckIn');
                const valIn = document.getElementById('sheetAttCheckInVal');
                if (rowIn && valIn) {
                    if (attCheckIn) {
                        rowIn.style.display = 'flex';
                        valIn.textContent = attCheckIn;
                    } else {
                        rowIn.style.display = 'none';
                    }
                }

                const rowOut = document.getElementById('sheetAttRowCheckOut');
                const valOut = document.getElementById('sheetAttCheckOutVal');
                if (rowOut && valOut) {
                    if (attCheckOut) {
                        rowOut.style.display = 'flex';
                        valOut.textContent = attCheckOut;
                    } else {
                        rowOut.style.display = 'none';
                    }
                }

                const rowInst = document.getElementById('sheetAttRowInstitute');
                const valInst = document.getElementById('sheetAttInstituteVal');
                if (rowInst && valInst) {
                    if (attInstitute) {
                        rowInst.style.display = 'flex';
                        valInst.textContent = attInstitute;
                    } else {
                        rowInst.style.display = 'none';
                    }
                }

                const linkBtn = document.getElementById('sheetAttLinkBtn');
                if (linkBtn && attUrl) linkBtn.href = attUrl;
            }
        } else if (structuredType === 'exam_result') {
            const examContainer = containers['exam_result'];
            if (examContainer) {
                examContainer.style.display = 'block';

                const examName = triggerEl.getAttribute('data-exam-name') || 'Examination';
                const examStudent = triggerEl.getAttribute('data-exam-student') || '-';
                const examClass = triggerEl.getAttribute('data-exam-class') || '-';
                const examDate = triggerEl.getAttribute('data-exam-date') || time;
                const examMsg = triggerEl.getAttribute('data-exam-msg') || content;
                const examUrl = triggerEl.getAttribute('data-exam-url') || '#';

                const heroVal = document.getElementById('sheetExamHeroVal');
                if (heroVal) heroVal.textContent = examName;

                const studentEl = document.getElementById('sheetExamStudentVal');
                if (studentEl) studentEl.textContent = examStudent;

                const classEl = document.getElementById('sheetExamClassVal');
                if (classEl) classEl.textContent = examClass;

                const dateEl = document.getElementById('sheetExamDateVal');
                if (dateEl) dateEl.textContent = examDate;

                const msgEl = document.getElementById('sheetExamMsgVal');
                if (msgEl) msgEl.textContent = examMsg;

                const linkBtn = document.getElementById('sheetExamLinkBtn');
                if (linkBtn && examUrl) linkBtn.href = examUrl;
            }
        } else if (structuredType === 'complaint') {
            const compContainer = containers['complaint'];
            if (compContainer) {
                compContainer.style.display = 'block';

                const compTicket = triggerEl.getAttribute('data-comp-ticket') || 'SUPPORT';
                const compStatus = triggerEl.getAttribute('data-comp-status') || 'Updated';
                const compSubject = triggerEl.getAttribute('data-comp-subject') || '';
                const compDate = triggerEl.getAttribute('data-comp-date') || time;
                const compMsg = triggerEl.getAttribute('data-comp-msg') || content;
                const compUrl = triggerEl.getAttribute('data-comp-url') || '#';

                const tagEl = document.getElementById('sheetCompTicketTag');
                if (tagEl) tagEl.innerHTML = '<i class="fa fa-ticket"></i> ' + compTicket;

                const statusTag = document.getElementById('sheetCompStatusTag');
                if (statusTag) statusTag.innerHTML = '<i class="fa fa-circle text-primary" style="font-size:7px;"></i> ' + compStatus;

                const heroVal = document.getElementById('sheetCompHeroVal');
                if (heroVal) heroVal.textContent = compStatus.toUpperCase();

                const ticketEl = document.getElementById('sheetCompTicketVal');
                if (ticketEl) ticketEl.textContent = compTicket;

                const rowSub = document.getElementById('sheetCompRowSubject');
                const valSub = document.getElementById('sheetCompSubjectVal');
                if (rowSub && valSub) {
                    if (compSubject) {
                        rowSub.style.display = 'flex';
                        valSub.textContent = compSubject;
                    } else {
                        rowSub.style.display = 'none';
                    }
                }

                const dateEl = document.getElementById('sheetCompDateVal');
                if (dateEl) dateEl.textContent = compDate;

                const msgEl = document.getElementById('sheetCompMsgVal');
                if (msgEl) msgEl.textContent = compMsg;

                const linkBtn = document.getElementById('sheetCompLinkBtn');
                if (linkBtn && compUrl) linkBtn.href = compUrl;
            }
        } else if (structuredType === 'fee_revert') {
            const revertContainer = containers['fee_revert'];
            if (revertContainer) {
                revertContainer.style.display = 'block';

                const revStudent = triggerEl.getAttribute('data-revert-student') || '-';
                const revReceipt = triggerEl.getAttribute('data-revert-receipt') || '';
                const revDate = triggerEl.getAttribute('data-revert-date') || time;
                const revMode = triggerEl.getAttribute('data-revert-mode') || 'Reversal';
                const revTotal = triggerEl.getAttribute('data-revert-total') || '0.00';
                const revDetails = triggerEl.getAttribute('data-revert-details') || '';
                const revUrl = triggerEl.getAttribute('data-revert-url') || '#';

                const recTag = document.getElementById('sheetRevertReceiptTag');
                if (recTag) recTag.innerHTML = revReceipt ? '<i class="fa fa-undo"></i> REC #' + revReceipt : '<i class="fa fa-undo"></i> Reverted';

                const heroVal = document.getElementById('sheetRevertHeroVal');
                if (heroVal) heroVal.innerHTML = '<i class="fa fa-inr"></i> ' + formatCurrency(revTotal);

                const studentEl = document.getElementById('sheetRevertStudentVal');
                if (studentEl) studentEl.textContent = revStudent;

                const dateEl = document.getElementById('sheetRevertDateVal');
                if (dateEl) dateEl.textContent = revDate;

                const modeEl = document.getElementById('sheetRevertModeVal');
                if (modeEl) modeEl.textContent = revMode;

                const rowDetails = document.getElementById('sheetRevertRowDetails');
                const valDetails = document.getElementById('sheetRevertDetailsVal');
                if (rowDetails && valDetails) {
                    if (revDetails) {
                        rowDetails.style.display = 'flex';
                        valDetails.textContent = revDetails;
                    } else {
                        rowDetails.style.display = 'none';
                    }
                }

                const linkBtn = document.getElementById('sheetRevertLinkBtn');
                if (linkBtn && revUrl) linkBtn.href = revUrl;
            }
        } else if (structuredType === 'notice') {
            const noticeContainer = containers['notice'];
            if (noticeContainer) {
                noticeContainer.style.display = 'block';

                const noticeTitle = triggerEl.getAttribute('data-notice-title') || title;
                const noticeDate = triggerEl.getAttribute('data-notice-date') || time;
                const noticeMsg = triggerEl.getAttribute('data-notice-msg') || content;
                const noticeUrl = triggerEl.getAttribute('data-notice-url') || '#';

                const dateTag = document.getElementById('sheetNoticeDateTag');
                if (dateTag) dateTag.innerHTML = '<i class="fa fa-clock-o"></i> ' + noticeDate;

                const heroVal = document.getElementById('sheetNoticeHeroVal');
                if (heroVal) heroVal.textContent = noticeTitle;

                const msgEl = document.getElementById('sheetNoticeMsgVal');
                if (msgEl) msgEl.textContent = noticeMsg;

                const attachBtn = document.getElementById('sheetNoticeAttachBtn');
                if (attachBtn) {
                    if (attachment && attachment.trim() !== '') {
                        attachBtn.href = attachment;
                        attachBtn.style.display = 'inline-flex';
                        attachBtn.innerHTML = '<i class="fa fa-paperclip"></i> ' + (attachmentName || 'Download Attachment');
                    } else {
                        attachBtn.style.display = 'none';
                    }
                }

                const linkBtn = document.getElementById('sheetNoticeLinkBtn');
                if (linkBtn && noticeUrl) linkBtn.href = noticeUrl;
            }
        } else {
            const stdContainer = containers['standard'];
            if (stdContainer) stdContainer.style.display = 'block';

            const bodyEl = document.getElementById('sheetNotifBody');
            if (bodyEl) bodyEl.textContent = content;

            // Attachment
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
    } catch (err) {
        console.error('Error opening notification sheet:', err);
        const backdrop = document.getElementById('mobNotifBackdrop');
        const sheet = document.getElementById('mobNotifSheet');
        if (backdrop) backdrop.classList.add('show');
        if (sheet) sheet.classList.add('show');
        document.body.classList.add('mob-sheet-lock');
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
