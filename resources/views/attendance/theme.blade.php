<style>
    :root {
        --att-bg: #f4f6f9;
        --att-card: #ffffff;
        --att-text: #1e293b;
        --att-muted: #64748b;
        --att-border: #cbd5e1;
        --att-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        --att-shadow-strong: 0 2px 8px rgba(0, 44, 84, 0.12);
        --att-primary: #002C54;
        --att-primary-dark: #001f3f;
        --att-primary-light: #0f3460;
        --att-success: #10b981;
        --att-warning: #f59e0b;
        --att-danger: #ef4444;
        --att-radius: 2px;
    }

    .attendance-page {
        background: var(--att-bg);
        color: var(--att-text);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 12px;
    }

    .attendance-page .content-wrapper {
        background: transparent;
    }

    .attendance-shell {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* Signature ARISE Navy Hero Banner */
    .attendance-hero {
        background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
        color: #fff !important;
        border-radius: 2px !important;
        padding: 6px 12px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .attendance-hero-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .attendance-hero-kicker {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .82;
        font-weight: 600;
        line-height: 1.2;
    }

    .attendance-hero-title {
        font-size: 14px !important;
        line-height: 1.2;
        font-weight: 700 !important;
        margin: 1px 0 !important;
        color: #ffffff !important;
    }

    .attendance-hero-subtitle {
        font-size: 11px;
        line-height: 1.35;
        margin: 0;
        opacity: .85;
        color: #e2e8f0;
    }

    .attendance-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    /* Chips, Pills & Header Badges */
    .attendance-chip,
    .att-chip,
    .att-pill,
    .self-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 2px !important;
        padding: 2px 8px !important;
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 600;
        height: 25px;
        line-height: 23px;
    }

    .att-header-badge {
        display: inline-block;
        padding: 1px 6px;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #fff;
        border-radius: 2px !important;
    }

    .att-date-pill {
        background: #fff !important;
        border: 1px solid #cbd5e1 !important;
        color: #1e293b !important;
        padding: 1px 6px !important;
        height: 27px !important;
    }

    .att-date-input {
        border: none;
        background: transparent;
        font-size: 11.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        height: 25px;
        padding: 0 4px;
    }

    /* Card Layouts */
    .attendance-page .card {
        border: 1px solid #cbd5e1 !important;
        border-radius: 2px !important;
        box-shadow: none !important;
        margin-bottom: 12px;
        background: #ffffff;
    }

    .attendance-page .card.card-outline.card-orange {
        border-top: 3px solid #002C54 !important;
        border-radius: 2px !important;
        border-left: 1px solid #cbd5e1 !important;
        border-right: 1px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }

    .attendance-page .card-header {
        padding: 6px 12px !important;
        min-height: 38px;
        border-bottom: 1px solid #cbd5e1 !important;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .attendance-page .card-header.bg-primary {
        background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
        color: #fff !important;
        border-radius: 0 !important;
        border-bottom: 1px solid #002C54 !important;
    }

    .attendance-page .card-header.bg-primary .card-title,
    .attendance-page .card-header.bg-primary .att-title {
        color: #fff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        margin: 0;
    }

    .attendance-page .card-header .card-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        margin: 0;
        color: #1e293b;
    }

    .attendance-page .card-body {
        padding: 12px !important;
    }

    .attendance-page .card-footer {
        padding: 6px 12px !important;
        background: #f8fafc;
        border-top: 1px solid #cbd5e1;
    }

    /* Custom attendance cards & toolbars */
    .attendance-card,
    .att-filter-row,
    .attendance-filter,
    .att-actions,
    .attendance-toolbar {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 2px !important;
        box-shadow: none !important;
    }

    .attendance-card {
        padding: 12px;
    }

    .attendance-card h5 {
        font-size: 12.5px;
        font-weight: 700;
        margin: 0 0 8px;
        color: #002C54;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .attendance-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        padding: 8px 10px;
        margin-bottom: 10px;
    }

    .attendance-toolbar label,
    .att-field label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Compact Form Inputs & Selects */
    .attendance-page .form-control,
    .attendance-page select,
    .attendance-page .att-filter,
    .attendance-toolbar .form-control,
    .att-filter-row .form-control {
        height: 29px !important;
        min-height: 29px !important;
        line-height: 27px;
        padding: 1px 8px !important;
        font-size: 11.5px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 2px !important;
        color: #1e293b;
        background-color: #fff;
        transition: border-color .15s ease-in-out;
    }

    .attendance-page .form-control:focus,
    .attendance-page select:focus {
        border-color: #002C54 !important;
        outline: none;
        box-shadow: 0 0 0 1px rgba(0, 44, 84, 0.2) !important;
    }

    .attendance-page textarea.form-control {
        height: auto !important;
        min-height: 60px !important;
        line-height: 1.4 !important;
        padding: 6px 8px !important;
    }

    /* Select2 High-Precision Overrides */
    .attendance-page .select2-container .select2-selection--single {
        height: 29px !important;
        min-height: 29px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 2px !important;
        padding: 0;
    }

    .attendance-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 27px !important;
        font-size: 11.5px !important;
        padding-left: 8px !important;
        color: #1e293b !important;
    }

    .attendance-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 27px !important;
        right: 4px !important;
    }

    .attendance-page .select2-container--default .select2-selection--multiple {
        min-height: 29px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 2px !important;
        padding: 1px 4px !important;
    }

    .attendance-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e0f2fe !important;
        border: 1px solid #bae6fd !important;
        color: #0369a1 !important;
        border-radius: 2px !important;
        font-size: 11px !important;
        margin-top: 2px !important;
        margin-bottom: 2px !important;
        padding: 1px 6px !important;
        font-weight: 600 !important;
        line-height: 18px !important;
    }

    .attendance-page .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #0369a1 !important;
        margin-right: 4px !important;
    }

    /* Buttons */
    .attendance-page .btn {
        border-radius: 2px !important;
        height: 27px;
        line-height: 25px;
        padding: 0 10px;
        font-size: 11px !important;
        font-weight: 600;
        letter-spacing: .2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all .15s ease-in-out;
    }

    .attendance-page .btn-sm {
        height: 24px;
        line-height: 22px;
        padding: 0 8px;
        font-size: 10.5px !important;
    }

    .attendance-page .btn.btn-primary {
        background: #002C54 !important;
        border-color: #002C54 !important;
        color: #ffffff !important;
    }

    .attendance-page .btn.btn-primary:hover {
        background: #0f3460 !important;
        border-color: #0f3460 !important;
    }

    .attendance-page .btn.btn-outline-secondary,
    .attendance-page .btn.btn-outline-light {
        background: #fff;
        color: #334155;
        border-color: #cbd5e1;
    }

    .attendance-page .btn.btn-outline-secondary:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .attendance-page .btn.btn-success {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #fff !important;
    }

    .attendance-page .btn.btn-danger {
        background: #ef4444 !important;
        border-color: #ef4444 !important;
        color: #fff !important;
    }

    .attendance-page .btn.btn-warning {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #fff !important;
    }

    /* Tabs */
    .att-tabs {
        gap: 4px;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 6px;
    }

    .att-tabs .nav-link {
        border-radius: 2px !important;
        padding: 4px 10px;
        font-weight: 600;
        font-size: 11.5px;
        border: 1px solid #cbd5e1;
        color: #475569;
        background: #f8fafc;
        transition: all .15s ease-in-out;
    }

    .att-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #002C54;
    }

    .att-tabs .nav-link.active {
        background: #002C54 !important;
        color: #fff !important;
        border-color: #002C54 !important;
    }

    /* High-Precision Tables */
    .attendance-table,
    .att-table,
    .table-attendance {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        background: #fff;
        font-size: 11.5px;
        margin-bottom: 0;
    }

    .attendance-table th,
    .att-table th,
    .table-attendance th,
    .attendance-page .table thead th {
        background: #002C54 !important;
        color: #ffffff !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: .04em;
        border: 1px solid #1a3a60 !important;
        padding: 5px 8px !important;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }

    .attendance-table td,
    .att-table td,
    .table-attendance td,
    .attendance-page .table tbody td {
        vertical-align: middle;
        border: 1px solid #e2e8f0 !important;
        padding: 4px 8px !important;
        font-size: 11.5px;
        color: #1e293b;
    }

    .attendance-page .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8fafc;
    }

    .attendance-page .table tbody tr:hover {
        background-color: #f1f5f9;
    }

    /* Badges & Status Indicators */
    .badge {
        border-radius: 2px !important;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 6px;
        letter-spacing: 0.2px;
    }

    .badge-in,
    .badge-success,
    .status-present,
    .status-in {
        background-color: #10b981 !important;
        color: #fff !important;
    }

    .badge-absent,
    .badge-danger,
    .status-absent {
        background-color: #ef4444 !important;
        color: #fff !important;
    }

    .badge-holiday,
    .badge-warning,
    .status-holiday {
        background-color: #f59e0b !important;
        color: #fff !important;
    }

    .badge-halfday,
    .status-halfday {
        background-color: #8b5cf6 !important;
        color: #fff !important;
    }

    /* Stats Grid */
    .attendance-stat-grid,
    .self-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .attendance-stat,
    .self-stat-card {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 2px !important;
        box-shadow: none;
        padding: 8px 10px;
    }

    .attendance-stat .label,
    .self-stat-card .label {
        display: block;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--att-muted);
        margin-bottom: 2px;
        font-weight: 700;
    }

    .attendance-stat strong,
    .self-stat-card .value {
        display: block;
        font-size: 16px;
        line-height: 1.1;
        color: #002C54;
        font-weight: 800;
    }

    /* Calendar & History View */
    .att-calendar {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
    }

    .att-day {
        border: 1px solid #cbd5e1;
        border-radius: 2px !important;
        min-height: 60px;
        padding: 6px;
        background: #fff;
        position: relative;
        cursor: pointer;
        transition: all .15s ease;
    }

    .att-day:hover {
        border-color: #002C54;
        background: #f8fafc;
    }

    .att-day.empty {
        background: #f8fafc;
        border-style: dashed;
        cursor: default;
    }

    .att-day .date {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
    }

    .att-day .time {
        font-size: 10px;
        color: var(--att-muted);
    }

    .att-day .status-dot {
        position: absolute;
        right: 6px;
        top: 6px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .att-day.active {
        border-color: #002C54;
        box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15);
        background: #f0f7ff;
    }

    .att-summary .small-box {
        border-radius: 2px !important;
        overflow: hidden;
        box-shadow: none !important;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Marking Window Tabs */
    .marking-tabs {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .marking-tab {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 2px !important;
        color: #475569;
        text-decoration: none !important;
        transition: all .15s ease-in-out;
    }

    .marking-tab:hover {
        color: #002C54;
        border-color: #94a3b8;
    }

    .marking-tab.active {
        border-color: #002C54 !important;
        background: #f0f7ff !important;
        color: #002C54 !important;
        font-weight: 700;
    }

    .marking-tab > i {
        font-size: 16px;
        color: #002C54;
    }

    .marking-tab span {
        font-weight: 700;
        font-size: 12px;
    }

    .marking-tab small {
        display: block;
        font-size: 10px;
        font-weight: 400;
        opacity: .8;
        margin-top: 1px;
    }

    @media (max-width: 991.98px) {
        .attendance-stat-grid,
        .self-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .attendance-hero {
            padding: 8px 10px !important;
        }

        .attendance-stat-grid {
            grid-template-columns: 1fr;
        }

        .att-calendar {
            gap: 4px;
        }
    }
</style>
