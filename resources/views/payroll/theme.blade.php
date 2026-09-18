<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap');

    .payroll-page {
        --payroll-ink: #0b1b2b;
        --payroll-muted: #5b6b7c;
        --payroll-primary: #0f3d56;
        --payroll-primary-2: #145c7a;
        --payroll-accent: #f59e0b;
        --payroll-accent-2: #16a34a;
        --payroll-bg: #f4f6fb;
        --payroll-card: #ffffff;
        --payroll-border: #e2e8f0;
        --payroll-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        --payroll-shadow-soft: 0 6px 18px rgba(15, 23, 42, 0.08);
        font-family: 'Manrope', 'Segoe UI', Tahoma, sans-serif;
        color: var(--payroll-ink);
    }

    .payroll-hero {
        position: relative;
        border-radius: 18px;
        padding: 24px;
        background: linear-gradient(120deg, #0f3d56 0%, #145c7a 45%, #1f8a70 100%);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: var(--payroll-shadow);
        overflow: hidden;
    }

    .payroll-hero:before,
    .payroll-hero:after {
        content: "";
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .payroll-hero:before {
        width: 280px;
        height: 280px;
        right: -80px;
        top: -140px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.35), rgba(245, 158, 11, 0) 70%);
    }

    .payroll-hero:after {
        width: 240px;
        height: 240px;
        left: -90px;
        bottom: -120px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0) 70%);
    }

    .payroll-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        align-items: center;
        justify-content: space-between;
    }

    .payroll-hero-kicker {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 700;
    }

    .payroll-hero-title {
        font-family: 'Playfair Display', 'Times New Roman', serif;
        font-size: 30px;
        font-weight: 700;
        margin: 4px 0;
    }

    .payroll-hero-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        margin-bottom: 8px;
    }

    .payroll-hero-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .payroll-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 12px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    .payroll-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
    }

    .btn-payroll {
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.2px;
        padding: 6px 14px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .btn-payroll-light {
        background: rgba(255, 255, 255, 0.92);
        color: #0b1b2b;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-payroll-light:hover {
        transform: translateY(-1px);
        background: #fff;
        color: #0b1b2b;
    }

    .btn-payroll-primary {
        background: #0b1b2b;
        color: #fff;
        border-color: rgba(11, 27, 43, 0.5);
    }

    .btn-payroll-primary:hover {
        transform: translateY(-1px);
        background: #0a1623;
        color: #fff;
    }

    .btn-payroll-accent {
        background: var(--payroll-accent);
        color: #1b1202;
        border-color: rgba(245, 158, 11, 0.6);
    }

    .btn-payroll-accent:hover {
        transform: translateY(-1px);
        background: #fbbf24;
        color: #1b1202;
    }

    .btn-payroll-outline {
        background: transparent;
        color: #1f2937;
        border-color: #cbd5e1;
    }

    .btn-payroll-outline:hover {
        background: #f1f5f9;
        color: #111827;
    }

    .btn-payroll-success {
        background: rgba(22, 163, 74, 0.12);
        color: #166534;
        border-color: rgba(22, 163, 74, 0.3);
    }

    .btn-payroll-success:hover {
        background: rgba(22, 163, 74, 0.2);
        color: #166534;
    }

    .btn-payroll-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #991b1b;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .btn-payroll-danger:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #991b1b;
    }

    .payroll-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin: 16px 0 14px;
    }

    .payroll-stat {
        background: var(--payroll-card);
        border: 1px solid var(--payroll-border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: var(--payroll-shadow-soft);
    }

    .payroll-stat-success {
        background: #ecfdf5;
        border-color: #bbf7d0;
    }

    .payroll-stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: var(--payroll-muted);
        font-weight: 700;
    }

    .payroll-stat-value {
        font-size: 20px;
        font-weight: 800;
        margin: 6px 0 2px;
        color: var(--payroll-ink);
    }

    .payroll-stat-sub {
        font-size: 12px;
        color: var(--payroll-muted);
    }

    .payroll-card {
        border-radius: 16px;
        border: 1px solid var(--payroll-border);
        box-shadow: var(--payroll-shadow-soft);
        overflow: hidden;
    }

    .payroll-card-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid var(--payroll-border);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
    }

    .payroll-card-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .payroll-subcard {
        border-radius: 14px;
        border: 1px solid var(--payroll-border);
        box-shadow: var(--payroll-shadow-soft);
        overflow: hidden;
        margin-top: 16px;
    }

    .payroll-subcard .card-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--payroll-border);
        font-weight: 700;
    }

    .payroll-filter {
        background: #f8fafc;
        border: 1px solid var(--payroll-border);
        border-radius: 12px;
        padding: 12px;
        gap: 10px;
    }

    .payroll-filter .form-control {
        border-radius: 10px;
        border: 1px solid #d1d9e6;
        box-shadow: none;
    }

    .payroll-table {
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
    }

    .payroll-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #475569;
        white-space: nowrap;
        background: #f1f5f9;
        border-bottom: 1px solid var(--payroll-border) !important;
    }

    .payroll-table td {
        vertical-align: middle;
        border-color: #eef2f7 !important;
    }

    .payroll-table tbody tr:hover {
        background: #f8fbff;
    }

    .payroll-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 16px;
        padding: 4px 10px;
        font-weight: 600;
        font-size: 12px;
        color: #374151;
    }

    .badge-soft {
        border: 1px solid #e5e7eb;
        background: #f8f9fa;
        color: #495057;
        border-radius: 14px;
        padding: 4px 10px;
        font-weight: 600;
    }

    .badge-payroll {
        border-radius: 999px;
        padding: 4px 10px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .badge-payroll-success {
        background: rgba(22, 163, 74, 0.12);
        color: #166534;
        border: 1px solid rgba(22, 163, 74, 0.3);
    }

    .badge-payroll-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #92400e;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-payroll-muted {
        background: rgba(100, 116, 139, 0.12);
        color: #334155;
        border: 1px solid rgba(100, 116, 139, 0.3);
    }

    .payroll-inline-note {
        font-size: 12px;
        color: var(--payroll-muted);
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px !important;
        border: 1px solid #d1d9e6 !important;
        padding: 4px 8px;
        margin-left: 6px;
    }

    .modal.payroll-modal .modal-content {
        border-radius: 16px;
        border: 1px solid var(--payroll-border);
        box-shadow: var(--payroll-shadow);
    }

    .modal.payroll-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--payroll-border);
    }

    @media (max-width: 768px) {
        .payroll-hero {
            padding: 18px;
        }

        .payroll-hero-title {
            font-size: 24px;
        }

        .payroll-hero-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
