@php
    $setting = DB::table('settings')->first();
    $schoolName = $setting->name ?? 'School ERP';
    $logoFileName = $setting->left_logo ?? '';
    $logoUrl = !empty($logoFileName)
        ? rtrim((string) env('IMAGE_SHOW_PATH'), '/') . '/setting/left_logo/' . rawurlencode($logoFileName)
        : '';
    $schoolInitial = strtoupper(mb_substr(trim($schoolName), 0, 1, 'UTF-8')) ?: 'A';
    $faviconUrl = !empty($logoUrl) ? $logoUrl : asset('public/assets/school/img/logo.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#002C54">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $schoolName }} | Sign In</title>
    <link rel="icon" href="{{ $faviconUrl }}">

    <!-- Typography & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Arise Theme Core & Modals -->
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}?v=1.0.4">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-modals.css') }}?v=1.0.4">

    <script src="{{ asset('public/assets/school/js/jquery.min.js') }}"></script>

    <style>
        /* ==============================================================================
           ARISE ERP - SIGNATURE AUTHENTICATION LAYOUT (Strict Theme Compliance)
           Adheres strictly to arise-theme.css, admissionView, and addUser standards:
           - Palette: #002C54 (Primary Navy), #0f3460 (Secondary), #002342 (Header), #2563eb (Accent)
           - Border Radius: Sharp 3px throughout (Cards, Inputs, Buttons, Modals, Tabs)
           - Typography: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Inter', Roboto, sans-serif
           - Font Sizes: Compact 12px - 13px base, 11px labels, 13.5px headers
           - Controls: 33px input height (desktop), 36px (mobile for native tap accuracy)
           ============================================================================== */

        :root {
            --arise-primary: #002C54;
            --arise-primary-hover: #001f3d;
            --arise-secondary: #0f3460;
            --arise-header-dark: #002342;
            --arise-accent: #2563eb;
            --arise-accent-hover: #1d4ed8;
            --arise-success: #059669;
            --arise-danger: #dc2626;
            --arise-warning: #d97706;
            --arise-info: #0284c7;
            --arise-border: #e2e8f0;
            --arise-border-dark: #cbd5e1;
            --arise-bg-body: #eef2f6;
            --arise-bg-card: #ffffff;
            --arise-text-main: #0f172a;
            --arise-text-muted: #64748b;
            --radius-erp: 3px;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Inter', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 13px !important;
            color: var(--arise-text-main);
            background-color: var(--arise-bg-body) !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body.arise-login-body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 16px 12px;
            background: #eef2f6;
        }

        /* Master Container */
        .arise-auth-wrapper {
            width: 100%;
            max-width: 860px;
            background: var(--arise-bg-card);
            border: 1px solid var(--arise-border-dark);
            border-radius: var(--radius-erp);
            box-shadow: 0 2px 10px rgba(0, 44, 84, 0.08);
            display: grid;
            grid-template-columns: 340px 1fr;
            overflow: hidden;
        }

        /* --------------------------------------------------------------------------
           LEFT PANEL: Institutional ERP Brand Showcase (Strict Navy Theme)
           -------------------------------------------------------------------------- */
        .arise-showcase-column {
            background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
            color: #ffffff;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* School Logo & Title */
        .showcase-brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }
        .showcase-logo-frame {
            width: 48px;
            height: 48px;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: var(--radius-erp);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        .showcase-logo-frame img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }
        .firm-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
            color: #ffffff;
            font-size: 19px;
            font-weight: 800;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            text-transform: uppercase;
            line-height: 1;
            border-radius: var(--radius-erp);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
            user-select: none;
        }
        .showcase-brand-titles {
            overflow: hidden;
        }
        .showcase-kicker {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #93c5fd;
            display: block;
            margin-bottom: 1px;
        }
        .showcase-school-name {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.25;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Value Propositions */
        .showcase-body {
            margin-bottom: 20px;
        }
        .showcase-headline {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .showcase-desc {
            font-size: 11.5px;
            line-height: 1.45;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
        .showcase-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .showcase-feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11.5px;
            color: #f1f5f9;
        }
        .showcase-feature-item i {
            color: #38bdf8;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* Showcase Footer Stats */
        .showcase-footer {
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10.5px;
            color: #cbd5e1;
        }
        .showcase-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--radius-erp);
            padding: 2px 7px;
            font-size: 10.5px;
            color: #ffffff;
        }
        .showcase-pill i {
            color: #10b981;
        }

        /* --------------------------------------------------------------------------
           RIGHT PANEL: Form Card Layout (Strict Theme Controls)
           -------------------------------------------------------------------------- */
        .arise-form-column {
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }
        .arise-form-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Card Header (Standard #002342 Navy Bar) */
        .arise-card-header {
            background: var(--arise-header-dark);
            color: #ffffff;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            min-height: 38px;
        }
        .arise-header-title {
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .arise-header-title i {
            color: #38bdf8;
            font-size: 14px;
        }
        .arise-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 1px 6px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            border-radius: var(--radius-erp);
            font-size: 10px;
            font-weight: 600;
            color: #a7f3d0;
        }

        /* Form Card Body */
        .arise-card-body {
            padding: 20px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Theme Segmented Tabs (Sharp Button Group) */
        .arise-auth-tabs {
            display: flex;
            background: var(--arise-bg-body);
            border: 1px solid var(--arise-border-dark);
            border-radius: var(--radius-erp);
            padding: 2px;
            gap: 2px;
            margin-bottom: 16px;
        }
        .arise-tab-btn {
            flex: 1;
            height: 30px;
            border: 1px solid transparent;
            border-radius: 2px;
            background: transparent;
            color: var(--arise-text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .arise-tab-btn i {
            font-size: 12.5px;
        }
        .arise-tab-btn.active {
            background: #ffffff;
            color: var(--arise-primary);
            border-color: var(--arise-border-dark);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .arise-tab-btn:hover:not(.active) {
            color: var(--arise-text-main);
            background: rgba(0, 0, 0, 0.02);
        }

        /* Tab Panes */
        .arise-tab-pane {
            display: none;
        }
        .arise-tab-pane.active {
            display: block;
        }

        /* Strict Form Controls (Matching arise-theme.css & admissionView) */
        .arise-form-group {
            margin-bottom: 12px;
        }
        .arise-form-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }
        .arise-input-group {
            display: flex;
            width: 100%;
            border-radius: var(--radius-erp);
        }
        .arise-input-prepend {
            width: 33px;
            height: 33px;
            background: #f8fafc;
            border: 1px solid var(--arise-border-dark);
            border-right: none;
            border-radius: var(--radius-erp) 0 0 var(--radius-erp);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 13px;
            flex-shrink: 0;
            transition: border-color 0.15s ease;
        }
        .arise-input-control {
            flex: 1;
            height: 33px;
            padding: 4px 9px;
            font-size: 13px;
            font-family: inherit;
            color: var(--arise-text-main);
            background-color: #ffffff;
            border: 1px solid var(--arise-border-dark);
            border-radius: 0 var(--radius-erp) var(--radius-erp) 0 !important;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .arise-input-group.has-append .arise-input-control {
            border-radius: 0 !important;
        }
        .arise-input-append {
            width: 33px;
            height: 33px;
            background: #ffffff;
            border: 1px solid var(--arise-border-dark);
            border-left: none;
            border-radius: 0 var(--radius-erp) var(--radius-erp) 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            outline: none;
            transition: color 0.15s ease;
        }
        .arise-input-append:hover {
            color: var(--arise-text-main);
        }
        .arise-input-group:focus-within .arise-input-prepend,
        .arise-input-group:focus-within .arise-input-control,
        .arise-input-group:focus-within .arise-input-append {
            border-color: var(--arise-primary);
        }
        .arise-input-control:focus {
            box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
        }

        .arise-field-error {
            display: block;
            min-height: 14px;
            margin-top: 3px;
            font-size: 11px;
            font-weight: 600;
            color: var(--arise-danger);
        }

        /* 4-Digit mPIN Layout (Sharp 3px Boxes) */
        .arise-mpin-container {
            text-align: center;
            padding: 4px 0 8px;
        }
        .arise-mpin-help {
            font-size: 11.5px;
            color: var(--arise-text-muted);
            margin-bottom: 12px;
        }
        .arise-mpin-grid {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .arise-mpin-box {
            width: 44px;
            height: 42px;
            border: 1px solid var(--arise-border-dark);
            border-radius: var(--radius-erp);
            background: #ffffff;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: var(--arise-primary);
            outline: none;
            transition: all 0.15s ease;
        }
        .arise-mpin-box:focus {
            border-color: var(--arise-primary);
            box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15);
            background: #f8fafc;
        }

        /* Buttons (Strict Arise ERP Standard) */
        .btn-arise-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            height: 34px;
            padding: 0 12px;
            font-size: 12.5px;
            font-weight: 600;
            color: #ffffff;
            background-color: var(--arise-primary);
            border: 1px solid var(--arise-primary);
            border-radius: var(--radius-erp) !important;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-arise-primary:hover {
            background-color: var(--arise-primary-hover);
            border-color: var(--arise-primary-hover);
        }
        .btn-arise-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Action Links Bar */
        .arise-auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid var(--arise-border);
        }
        .arise-link-btn {
            background: transparent;
            border: none;
            padding: 0;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--arise-accent);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.15s ease;
        }
        .arise-link-btn:hover {
            color: var(--arise-accent-hover);
            text-decoration: underline;
        }

        /* Support Strip */
        .arise-support-strip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            background: #f8fafc;
            border: 1px solid var(--arise-border);
            border-radius: var(--radius-erp);
            margin-top: 14px;
            font-size: 11px;
            color: var(--arise-text-muted);
        }
        .arise-support-strip i {
            color: var(--arise-info);
            font-size: 13px;
            flex-shrink: 0;
        }

        /* Footer Copyright */
        .arise-auth-footer {
            margin-top: 14px;
            text-align: center;
            font-size: 10.5px;
            color: #94a3b8;
        }
        .arise-auth-footer strong {
            color: #475467;
        }

        /* ==============================================================================
           MOBILE VIEW - HEADER AT TOP, FOOTER AT BOTTOM, CREDENTIALS FORM CENTERED (< 768px)
           ============================================================================== */
        @media (max-width: 767.98px) {
            body.arise-login-body {
                display: flex !important;
                align-items: stretch !important;
                min-height: 100vh !important;
                min-height: 100dvh !important;
                padding: 0 !important;
                background: #eef2f6 !important;
            }

            .arise-auth-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                min-height: 100vh !important;
                min-height: 100dvh !important;
                display: flex !important;
                flex-direction: column !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: #eef2f6 !important;
                margin: 0 !important;
            }

            /* TOP HEADER: School Brand (At Top of Screen) */
            .arise-showcase-column {
                padding: calc(12px + var(--safe-top)) 16px 12px 16px !important;
                border-right: none !important;
                border-bottom: 2px solid #001f3d !important;
                flex: 0 0 auto !important;
                box-shadow: 0 2px 6px rgba(0, 44, 84, 0.2) !important;
                background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
            }
            .showcase-brand-header {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                border-bottom: none !important;
                gap: 10px !important;
            }
            .showcase-logo-frame {
                width: 42px !important;
                height: 42px !important;
                padding: 3px !important;
            }
            .firm-avatar-fallback {
                font-size: 16px !important;
            }
            .showcase-kicker {
                font-size: 9px !important;
            }
            .showcase-school-name {
                font-size: 14px !important;
            }
            .showcase-body,
            .showcase-footer {
                display: none !important;
            }

            /* MIDDLE STAGE: Contains the centered credentials form card */
            .arise-form-column {
                flex: 1 1 auto !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 12px 14px !important;
                background: transparent !important;
                min-height: 0 !important;
                overflow-y: auto !important;
            }

            /* ONLY CREDENTIALS FORM CARD - NATURAL HEIGHT & EQUAL TOP/BOTTOM MARGINS */
            .arise-form-card {
                width: 100% !important;
                max-width: 440px !important;
                height: auto !important;
                min-height: auto !important;
                flex: 0 0 auto !important;
                margin: auto !important; /* Equal top and bottom margins (barabar) */
                background: #ffffff !important;
                border: 1px solid var(--arise-border-dark) !important;
                border-radius: var(--radius-erp) !important;
                box-shadow: 0 2px 8px rgba(0, 44, 84, 0.08) !important;
                overflow: hidden !important;
            }

            .arise-card-header {
                padding: 7px 12px !important;
                min-height: 35px !important;
            }
            .arise-header-title {
                font-size: 13px !important;
            }

            .arise-card-body {
                padding: 14px 14px !important;
                height: auto !important;
                min-height: auto !important;
                flex: 0 0 auto !important;
                justify-content: flex-start !important;
            }

            /* BOTTOM FOOTER: Stays at the bottom of the screen */
            .arise-auth-footer {
                flex: 0 0 auto !important;
                margin: 0 !important;
                padding: 6px 12px calc(8px + var(--safe-bottom)) 12px !important;
                text-align: center !important;
                font-size: 10.5px !important;
                color: #94a3b8 !important;
            }

            /* Touch Friendly Inputs while staying true to theme */
            .arise-input-prepend {
                width: 35px !important;
                height: 35px !important;
                font-size: 13.5px !important;
            }
            .arise-input-control {
                height: 35px !important;
                font-size: 13px !important;
            }
            .arise-input-append {
                width: 35px !important;
                height: 35px !important;
                font-size: 13.5px !important;
            }

            .btn-arise-primary {
                height: 35px !important;
                font-size: 12.5px !important;
            }

            .arise-tab-btn {
                height: 30px !important;
                font-size: 12px !important;
            }

            .arise-mpin-box {
                width: 40px !important;
                height: 40px !important;
                font-size: 18px !important;
            }
        }

        /* --------------------------------------------------------------------------
           MODAL DIALOGS - STRICT ALIGNMENT WITH arise-modals.css
           -------------------------------------------------------------------------- */
        .modal.arise-theme-modal .modal-dialog {
            max-width: 420px;
            margin: 1.75rem auto;
        }
        .modal.arise-theme-modal .modal-content {
            border: 1px solid rgba(0, 44, 84, 0.18) !important;
            border-radius: 4px !important;
            box-shadow: 0 20px 45px -8px rgba(0, 44, 84, 0.35) !important;
            overflow: hidden;
            background: #ffffff;
        }
        .modal.arise-theme-modal .modal-header {
            background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
            color: #ffffff !important;
            padding: 8px 14px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal.arise-theme-modal .modal-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .modal.arise-theme-modal .modal-header .close {
            color: #ffffff;
            opacity: 0.85;
            background: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            margin: 0;
            line-height: 1;
        }
        .modal.arise-theme-modal .modal-header .close:hover {
            opacity: 1;
        }
        .modal.arise-theme-modal .modal-body {
            padding: 16px 18px;
        }
    </style>
</head>
<body class="arise-login-body">

<div class="arise-auth-wrapper">

    <!-- LEFT PANEL: Institutional ERP Brand Showcase -->
    <aside class="arise-showcase-column">
        <div>
            <!-- School Brand Crest -->
            <div class="showcase-brand-header">
                <div class="showcase-logo-frame">
                    @if(!empty($logoUrl))
                        <img 
                            src="{{ $logoUrl }}" 
                            alt="{{ $schoolName }}"
                            loading="eager"
                            onerror="this.style.display='none'; var fb=document.getElementById('firmLogoAvatar'); if(fb){fb.style.display='flex';}"
                        >
                        <div class="firm-avatar-fallback" id="firmLogoAvatar" style="display: none;">
                            {{ $schoolInitial }}
                        </div>
                    @else
                        <div class="firm-avatar-fallback" id="firmLogoAvatar">
                            {{ $schoolInitial }}
                        </div>
                    @endif
                </div>
                <div class="showcase-brand-titles">
                    <span class="showcase-kicker">Unified Campus ERP</span>
                    <h1 class="showcase-school-name" title="{{ $schoolName }}">{{ $schoolName }}</h1>
                </div>
            </div>

            <!-- Value Proposition -->
            <div class="showcase-body">
                <div class="showcase-headline">Next-Gen Education Management</div>
                <p class="showcase-desc">High-performance academic, fees, attendance and student information management system.</p>
                
                <ul class="showcase-feature-list">
                    <li class="showcase-feature-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Ultra-fast real-time attendance sync</span>
                    </li>
                    <li class="showcase-feature-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Unified fee ledger & instant payment POS</span>
                    </li>
                    <li class="showcase-feature-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Multi-role access for staff, students & parents</span>
                    </li>
                    <li class="showcase-feature-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Encrypted sessions with instant 4-digit mPIN</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="showcase-footer">
            <span class="showcase-pill">
                <i class="fa fa-shield"></i> 256-Bit SSL Encrypted
            </span>
            <span class="showcase-pill">
                <i class="fa fa-server"></i> Arise Engine
            </span>
        </div>
    </aside>

    <!-- RIGHT PANEL: Authentication Card Layout -->
    <section class="arise-form-column">
        <div class="arise-form-card">
            <!-- Header Navy Strip -->
            <header class="arise-card-header">
            <h2 class="arise-header-title">
                <i class="fa fa-lock"></i> Account Sign In
            </h2>
            <span class="arise-header-badge">
                <i class="fa fa-check-circle"></i> Secure Portal
            </span>
        </header>

        <!-- Form Body -->
        <div class="arise-card-body">
            <div>
                <!-- Segmented Tabs: [ Password Login ] [ 4-Digit mPIN ] -->
                <nav class="arise-auth-tabs" role="tablist">
                    <button type="button" class="arise-tab-btn active" id="tabPassword" data-target="#panePassword" role="tab" aria-selected="true">
                        <i class="fa fa-user"></i> Username & Password
                    </button>
                    <button type="button" class="arise-tab-btn" id="tabMpin" data-target="#paneMpin" role="tab" aria-selected="false">
                        <i class="fa fa-th"></i> 4-Digit mPIN
                    </button>
                </nav>

                <!-- TAB 1: STANDARD USERNAME & PASSWORD FORM -->
                <div class="arise-tab-pane active" id="panePassword" role="tabpanel">
                    <form id="loginForm" method="POST" action="{{ url('login') }}" autocomplete="on" novalidate>
                        @csrf

                        <!-- Username Field -->
                        <div class="arise-form-group">
                            <label class="arise-form-label" for="user_name">
                                <span>Username / Admission No</span>
                            </label>
                            <div class="arise-input-group">
                                <span class="arise-input-prepend">
                                    <i class="fa fa-user-circle-o"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="user_name" 
                                    id="user_name" 
                                    class="arise-input-control" 
                                    placeholder="Enter username" 
                                    autocomplete="username" 
                                    required
                                >
                            </div>
                            <span class="arise-field-error" id="user_name_error"></span>
                        </div>

                        <!-- Password Field -->
                        <div class="arise-form-group">
                            <label class="arise-form-label" for="password">
                                <span>Security Password</span>
                            </label>
                            <div class="arise-input-group has-append">
                                <span class="arise-input-prepend">
                                    <i class="fa fa-key"></i>
                                </span>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    class="arise-input-control" 
                                    placeholder="Enter password" 
                                    autocomplete="current-password" 
                                    required
                                >
                                <button type="button" class="arise-input-append password-toggle" aria-label="Toggle password visibility">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <span class="arise-field-error" id="password_error"></span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="btn-arise-primary">
                            <i class="fa fa-sign-in"></i>
                            <span>Sign In Securely</span>
                        </button>
                    </form>
                </div>

                <!-- TAB 2: INLINE 4-DIGIT mPIN FORM -->
                <div class="arise-tab-pane" id="paneMpin" role="tabpanel">
                    <form id="mpinForm" method="POST" action="{{ url('mpin-login') }}">
                        @csrf
                        <div class="arise-mpin-container">
                            <p class="arise-mpin-help">Enter your 4-digit code linked with your account</p>
                            
                            <div class="arise-mpin-grid">
                                <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="arise-mpin-box mpin-input" aria-label="Digit 1" autofocus>
                                <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="arise-mpin-box mpin-input" aria-label="Digit 2">
                                <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="arise-mpin-box mpin-input" aria-label="Digit 3">
                                <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="arise-mpin-box mpin-input" aria-label="Digit 4">
                            </div>
                            <input type="hidden" name="mpin" id="final_mpin">
                        </div>

                        <button type="submit" id="mpinSubmitBtn" class="btn-arise-primary">
                            <i class="fa fa-shield"></i>
                            <span>Sign In with mPIN</span>
                        </button>
                    </form>
                </div>

                <!-- Secondary Actions: Generate mPIN Modal Trigger -->
                <div class="arise-auth-actions">
                    <button type="button" class="arise-link-btn" data-toggle="modal" data-target="#GenerateMpinModal">
                        <i class="fa fa-plus-circle"></i> Generate or Reset mPIN
                    </button>
                    <button type="button" class="arise-link-btn" id="quickTabToggle">
                        <span id="quickTabToggleText">Fast mPIN Mode</span> <i class="fa fa-angle-right"></i>
                    </button>
                </div>

                <!-- Support Box -->
                <div class="arise-support-strip">
                    <i class="fa fa-info-circle"></i>
                    <span>Contact administration to verify your admission no or reset credentials.</span>
                </div>
            </div>
        </div>
        </div>

        <!-- Footer Legal -->
        <footer class="arise-auth-footer">
            © {{ date('Y') }} <strong>{{ $schoolName }}</strong> · Arise ERP Platform
        </footer>
    </section>
</div>

<!-- ==============================================================================
     MODAL: GENERATE / RESET mPIN (Strict arise-modals.css Theme Compliance)
     ============================================================================== -->
<div class="modal fade arise-theme-modal" id="GenerateMpinModal" tabindex="-1" role="dialog" aria-labelledby="generateMpinTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="generateMpinTitle">
                    <i class="fa fa-shield"></i> Generate / Set 4-Digit mPIN
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="GenerateMpinForm">
                    @csrf
                    
                    <div class="arise-form-group">
                        <label class="arise-form-label" for="mpin_user">Username</label>
                        <div class="arise-input-group">
                            <span class="arise-input-prepend"><i class="fa fa-user"></i></span>
                            <input type="text" name="user_name" id="mpin_user" class="arise-input-control" placeholder="Enter username" autocomplete="username" required>
                        </div>
                    </div>

                    <div class="arise-form-group">
                        <label class="arise-form-label" for="mpin_pass">Current Password</label>
                        <div class="arise-input-group">
                            <span class="arise-input-prepend"><i class="fa fa-key"></i></span>
                            <input type="password" name="password" id="mpin_pass" class="arise-input-control" placeholder="Enter password" autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="arise-form-group">
                        <label class="arise-form-label" for="mpin_code">Set New 4-Digit mPIN</label>
                        <div class="arise-input-group">
                            <span class="arise-input-prepend"><i class="fa fa-th"></i></span>
                            <input type="tel" name="mpin" id="mpin_code" class="arise-input-control" maxlength="4" inputmode="numeric" pattern="[0-9]{4}" placeholder="Enter 4 digits" required>
                        </div>
                    </div>

                    <button type="submit" id="GenerateMpinBtn" class="btn-arise-primary" style="margin-top: 8px;">
                        <i class="fa fa-check"></i>
                        <span>Save Secure mPIN</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('student_login.layout.student_modal')

<script src="{{ asset('public/assets/school/js/bootstrap.bundle.min.js') }}"></script>
<script>
$(function(){
    // Setup AJAX CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Firm Logo Corrupted / Broken Image Fallback
    var $brandImg = $('.showcase-logo-frame img');
    if ($brandImg.length) {
        $brandImg.on('error', function(){
            $(this).hide();
            $('#firmLogoAvatar').css('display', 'flex');
        });
        if ($brandImg[0].complete && ($brandImg[0].naturalWidth === 0 || $brandImg[0].naturalHeight === 0)) {
            $brandImg.hide();
            $('#firmLogoAvatar').css('display', 'flex');
        }
    }

    // Tab switcher logic
    $('.arise-tab-btn').on('click', function(){
        var targetId = $(this).data('target');
        $('.arise-tab-btn').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');
        
        $('.arise-tab-pane').removeClass('active');
        $(targetId).addClass('active');

        if(targetId === '#paneMpin') {
            $('#quickTabToggleText').text('Password Mode');
            $('.arise-mpin-box').first().focus();
        } else {
            $('#quickTabToggleText').text('Fast mPIN Mode');
            $('#user_name').focus();
        }
    });

    $('#quickTabToggle').on('click', function(){
        if($('#panePassword').hasClass('active')) {
            $('#tabMpin').trigger('click');
        } else {
            $('#tabPassword').trigger('click');
        }
    });

    // Submit Busy Helper
    function loginBusy(loading) {
        $('#submitBtn').prop('disabled', loading).html(
            loading 
                ? '<i class="fa fa-circle-o-notch fa-spin"></i> <span>Verifying credentials...</span>' 
                : '<i class="fa fa-sign-in"></i> <span>Sign In Securely</span>'
        );
    }

    // Password Visibility Toggle
    $('.password-toggle').on('click', function(){
        var input = $('#password');
        var isPassword = input.attr('type') === 'password';
        input.attr('type', isPassword ? 'text' : 'password');
        $(this).find('i').attr('class', isPassword ? 'fa fa-eye-slash' : 'fa fa-eye');
    });

    // Login Form AJAX Submission
    $('#loginForm').on('submit', function(event){
        event.preventDefault();
        var form = $(this);
        $('#user_name_error, #password_error').text('');
        loginBusy(true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response){
                if (response.status === 'success') {
                    window.location.href = response.redirect_url;
                    return;
                }
                loginBusy(false);
                $('#user_name_error').text(response.user_name_error || '');
                $('#password_error').text(response.password_error || '');
                if (response.message) {
                    StudentModal.error('Sign In Failed', response.message);
                }
            },
            error: function(xhr){
                loginBusy(false);
                var response = xhr.responseJSON || {};
                var errors = response.errors || {};
                $('#user_name_error').text(errors.user_name ? errors.user_name[0] : '');
                $('#password_error').text(errors.password ? errors.password[0] : '');
                if (response.message) {
                    StudentModal.error('Sign In Failed', response.message);
                }
            }
        });
    });

    // 4-Digit mPIN Box Handling (Auto-advance & Backspace)
    $('.arise-mpin-box').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 1);
        if (this.value) {
            $(this).next('.arise-mpin-box').focus();
        }
        var fullPin = '';
        $('.arise-mpin-box').each(function(){
            fullPin += $(this).val();
        });
        $('#final_mpin').val(fullPin);

        // Auto submit when all 4 digits typed
        if(fullPin.length === 4) {
            $('#mpinForm').trigger('submit');
        }
    }).on('keydown', function(event){
        if (event.key === 'Backspace' && !this.value) {
            $(this).prev('.arise-mpin-box').focus();
        }
    });

    // mPIN Form AJAX Submit
    $('#mpinForm').on('submit', function(event){
        event.preventDefault();
        var pinValue = $('#final_mpin').val();
        var btn = $('#mpinSubmitBtn');

        if (pinValue.length !== 4) {
            StudentModal.error('Incomplete mPIN', 'Please enter all four digits.');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> <span>Verifying code...</span>');

        $.ajax({
            url: @json(url('mpin-login')),
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                mpin: pinValue
            },
            success: function(response){
                if (response.status === 'success') {
                    window.location.href = response.redirect_url;
                } else {
                    btn.prop('disabled', false).html('<i class="fa fa-shield"></i> <span>Sign In with mPIN</span>');
                    StudentModal.error('mPIN Login Failed', response.message || 'Invalid mPIN.');
                }
            },
            error: function(){
                btn.prop('disabled', false).html('<i class="fa fa-shield"></i> <span>Sign In with mPIN</span>');
                StudentModal.error('Unable to Sign In', 'Server error. Please try again.');
            }
        });
    });

    // Generate mPIN Modal Autofocus & numeric validation
    $('#GenerateMpinModal').on('shown.bs.modal', function(){
        $('#mpin_user').trigger('focus');
    });

    $('#mpin_code').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });

    // Generate mPIN Form Submit
    $('#GenerateMpinForm').on('submit', function(event){
        event.preventDefault();
        var form = this;
        var btn = $('#GenerateMpinBtn');

        btn.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> <span>Saving mPIN...</span>');

        $.ajax({
            url: @json(url('save-mpin')),
            type: 'POST',
            data: $(form).serialize(),
            success: function(response){
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> <span>Save Secure mPIN</span>');
                if (response.status === 'success') {
                    $('#GenerateMpinModal').modal('hide');
                    form.reset();
                    StudentModal.success('mPIN Created Successfully', response.message || 'Your secure mPIN is ready for sign in.');
                } else {
                    StudentModal.error('Could Not Create mPIN', response.message || 'Please check your credentials.');
                }
            },
            error: function(xhr){
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> <span>Save Secure mPIN</span>');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error. Please try again.';
                StudentModal.error('Could Not Create mPIN', msg);
            }
        });
    });
});

// Flash session alert notifications
document.addEventListener('DOMContentLoaded', function(){
    @if(Session::has('message'))
        StudentModal.open({
            type: 'success',
            title: 'Success',
            message: @json(session('message')),
            timer: 1800,
            showConfirm: false
        });
    @elseif(Session::has('error'))
        StudentModal.error('Unable to continue', @json(session('error')));
    @endif
});
</script>
@include('layout.csrf_refresh')
</body>
</html>
