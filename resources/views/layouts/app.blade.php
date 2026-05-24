<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hostel Fee Manager')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' rx='3' fill='%234f46e5'/><text x='2' y='13' font-size='13' font-family='sans-serif'>🏨</text></svg>">
    <!-- Bootstrap 5 -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="/css/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="/css/poppins.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --sidebar-bg: #1e1b4b;
            --sidebar-text: #c7d2fe;
            --sidebar-hover: #312e81;
            --sidebar-active: #4f46e5;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
        }

        * { font-family: 'Poppins', sans-serif; }

        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-brand small {
            color: var(--sidebar-text);
            font-size: 0.75rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-nav .nav-label {
            color: var(--primary-light);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.75rem 1.5rem 0.25rem;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary-light);
            font-weight: 500;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 1.5rem;
            flex-shrink: 0;
        }

        .sidebar-footer .user-info {
            color: #c7d2fe;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        /* ── Sidebar Overlay (mobile) ──────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 999;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
        }
        .sidebar-overlay.show { display: block; }

        /* ── Main content ─────────────────────────── */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .top-bar {
            background: var(--card-bg);
            padding: 0.875rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-bar h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text);
            font-size: 1rem;
        }

        .content-area {
            padding: 1.5rem 2rem;
        }

        /* ── Sidebar toggle button ─────────────────── */
        .sidebar-toggle-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.45rem;
            line-height: 1;
            padding: 0.2rem 0.45rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
            align-items: center;
            justify-content: center;
        }
        .sidebar-toggle-btn:hover { background: #f1f5f9; }

        /* ── Cards ────────────────────────────────── */
        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: box-shadow 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .card-custom {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* ── Table ────────────────────────────────── */
        .table-custom {
            margin: 0;
        }

        .table-custom thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }

        .table-custom tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table-custom tbody tr:hover {
            background: #f8fafc;
        }

        /* ── Badges ───────────────────────────────── */
        .badge-paid    { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-partial { background: #dbeafe; color: #1e40af; }

        /* ── Buttons ──────────────────────────────── */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* ── Form ─────────────────────────────────── */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* ── Mobile Bottom Navigation ─────────────── */
        .mobile-bottom-nav { display: none; }

        /* ── Flash Toasts ─────────────────────────── */
        .flash-toast-wrap {
            position: fixed;
            top: 1rem;
            right: 1rem;
            left: auto;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-width: 420px;
            width: calc(100% - 2rem);
            pointer-events: none;
        }

        .flash-toast {
            pointer-events: auto;
            position: relative;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18), 0 2px 6px rgba(15, 23, 42, 0.06);
            padding: 0.9rem 2.5rem 0.9rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            overflow: hidden;
            animation: flashSlideIn 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .flash-toast::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
        }

        .flash-toast.flash-success::before { background: var(--success); }
        .flash-toast.flash-error::before   { background: var(--danger); }

        .flash-toast .flash-icon {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .flash-toast.flash-success .flash-icon { background: #d1fae5; color: var(--success); }
        .flash-toast.flash-error   .flash-icon { background: #fee2e2; color: var(--danger); }

        .flash-toast .flash-body { flex: 1; min-width: 0; }

        .flash-toast .flash-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 0.15rem;
        }

        .flash-toast .flash-message {
            font-size: 0.825rem;
            color: var(--text-muted);
            line-height: 1.4;
            word-wrap: break-word;
        }

        .flash-toast .flash-message ul { margin: 0; padding-left: 1.1rem; }

        .flash-toast .flash-close {
            position: absolute;
            top: 0.55rem;
            right: 0.6rem;
            background: transparent;
            border: 0;
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1;
            padding: 0.25rem 0.45rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .flash-toast .flash-close:hover { background: #f1f5f9; color: var(--text); }

        .flash-toast .flash-progress {
            position: absolute;
            left: 0; bottom: 0;
            height: 3px;
            width: 100%;
            background: rgba(0,0,0,0.06);
        }

        .flash-toast .flash-progress > span {
            display: block;
            height: 100%;
            width: 100%;
            transform-origin: left center;
            animation: flashProgress 6s linear forwards;
        }

        .flash-toast.flash-success .flash-progress > span { background: var(--success); }
        .flash-toast.flash-error   .flash-progress > span { background: var(--danger); }

        .flash-toast.hide { animation: flashSlideOut 0.3s ease forwards; }

        @keyframes flashSlideIn {
            0%   { opacity: 0; transform: translateX(120%); }
            100% { opacity: 1; transform: translateX(0); }
        }

        @keyframes flashSlideOut {
            0%   { opacity: 1; transform: translateX(0); max-height: 200px; margin-bottom: 0.75rem; }
            100% { opacity: 0; transform: translateX(120%); max-height: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; border-width: 0; }
        }

        @keyframes flashProgress {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }

        /* ── Print ────────────────────────────────── */
        @media print {
            .sidebar, .top-bar, .no-print, .mobile-bottom-nav { display: none !important; }
            .main-content { margin-left: 0; }
            .content-area { padding: 0; }
        }

        /* ════════════════════════════════════════════
           MOBILE RESPONSIVE  (≤ 768px)
           ════════════════════════════════════════════ */
        @media (max-width: 768px) {

            /* Sidebar off-canvas */
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(15,23,42,0.25);
            }

            /* Show hamburger */
            .sidebar-toggle-btn { display: inline-flex; }

            /* Main content full-width */
            .main-content { margin-left: 0; }

            /* Top bar tighter */
            .top-bar {
                padding: 0.65rem 0.875rem;
                gap: 0.5rem;
            }
            .top-bar h5 { font-size: 0.9rem; }
            .top-bar .btn-sm {
                font-size: 0.72rem;
                padding: 0.28rem 0.55rem;
            }

            /* Content area – bottom padding for bottom nav */
            .content-area {
                padding: 1rem 0.875rem 5.5rem;
            }

            /* ── Mobile Bottom Navigation ── */
            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                background: #fff;
                border-top: 1px solid #e2e8f0;
                z-index: 998;
                box-shadow: 0 -4px 20px rgba(15,23,42,0.09);
                /* iOS safe area */
                padding-bottom: env(safe-area-inset-bottom, 0px);
            }

            .mobile-bottom-nav .mob-nav-item {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.18rem;
                padding: 0.55rem 0.25rem 0.45rem;
                color: var(--text-muted);
                text-decoration: none;
                font-size: 0.6rem;
                font-weight: 600;
                letter-spacing: 0.2px;
                border: none;
                background: none;
                cursor: pointer;
                transition: color 0.2s;
                position: relative;
                -webkit-tap-highlight-color: transparent;
            }

            .mobile-bottom-nav .mob-nav-item i {
                font-size: 1.3rem;
                transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            .mobile-bottom-nav .mob-nav-item.active {
                color: var(--primary);
            }

            .mobile-bottom-nav .mob-nav-item.active i {
                transform: translateY(-2px);
            }

            .mobile-bottom-nav .mob-nav-item.active::after {
                content: '';
                position: absolute;
                top: 0; left: 50%;
                transform: translateX(-50%);
                width: 28px; height: 3px;
                background: var(--primary);
                border-radius: 0 0 4px 4px;
            }

            /* ── Table → Card View ── */
            .table-responsive {
                overflow-x: visible !important;
                border: none !important;
            }

            .table-custom thead {
                display: none !important;
            }

            .table-custom,
            .table-custom tbody {
                display: block !important;
                width: 100% !important;
            }

            .table-custom tbody tr {
                display: block !important;
                border: 1px solid #e8edf3 !important;
                border-radius: 12px;
                margin-bottom: 0.75rem !important;
                padding: 0.25rem 0.875rem !important;
                background: #fff;
                box-shadow: 0 1px 6px rgba(15,23,42,0.06);
                transition: box-shadow 0.2s;
            }

            .table-custom tbody tr:hover {
                background: #fff !important;
                box-shadow: 0 4px 12px rgba(15,23,42,0.1);
            }

            /* Empty-state rows (colspan) – keep table look */
            .table-custom tbody tr.mob-empty-row {
                display: table-row !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }

            .table-custom tbody td {
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0 !important;
                border: none !important;
                border-bottom: 1px solid #f1f5f9 !important;
                font-size: 0.82rem;
                min-height: 38px;
                gap: 0.5rem;
            }

            .table-custom tbody td:last-child {
                border-bottom: none !important;
            }

            /* Label auto-injected by JS via data-label */
            .table-custom tbody td[data-label]::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.7rem;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.4px;
                flex-shrink: 0;
                min-width: 72px;
                margin-right: 0.5rem;
            }

            /* Empty-state cell */
            .table-custom tbody tr.mob-empty-row td {
                display: table-cell !important;
                border-bottom: 1px solid #f1f5f9 !important;
            }
            .table-custom tbody tr.mob-empty-row td::before { display: none !important; }

            /* ── Filter card collapsible header ── */
            .mob-filter-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.75rem;
                cursor: pointer;
                user-select: none;
            }

            .mob-filter-header-title {
                font-size: 0.875rem;
                font-weight: 600;
                color: var(--text);
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .mob-filter-toggle-btn {
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                padding: 0.25rem 0.6rem;
                font-size: 0.75rem;
                color: var(--text-muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.3rem;
                transition: background 0.15s;
            }

            .mob-filter-toggle-btn:hover { background: #e2e8f0; }

            /* ── Stat cards ── */
            .stat-card { padding: 1rem; }
            .stat-card .stat-value { font-size: 1.2rem; }
            .stat-card .stat-icon { width: 42px; height: 42px; font-size: 1.2rem; }

            /* ── Row gaps tighter ── */
            .row.g-4 { --bs-gutter-y: 0.875rem; }
            .row.g-3 { --bs-gutter-y: 0.75rem; }
        }

        /* ════════════════════════════════════════════
           EXTRA SMALL  (≤ 576px)
           ════════════════════════════════════════════ */
        @media (max-width: 576px) {
            .flash-toast-wrap {
                top: 0.5rem;
                right: 0.5rem;
                left: 0.5rem;
                width: auto;
                max-width: none;
            }
            .flash-toast {
                padding: 0.8rem 2.25rem 0.8rem 0.85rem;
                border-radius: 10px;
            }
            .flash-toast .flash-icon { width: 34px; height: 34px; font-size: 1rem; }
            .flash-toast .flash-title  { font-size: 0.85rem; }
            .flash-toast .flash-message { font-size: 0.78rem; }

            @keyframes flashSlideIn {
                0%   { opacity: 0; transform: translateY(-120%); }
                100% { opacity: 1; transform: translateY(0); }
            }
            @keyframes flashSlideOut {
                0%   { opacity: 1; transform: translateY(0); max-height: 200px; margin-bottom: 0.75rem; }
                100% { opacity: 0; transform: translateY(-120%); max-height: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; border-width: 0; }
            }

            .top-bar h5 { font-size: 0.85rem; }
            .content-area { padding: 0.75rem 0.75rem 5.5rem; }

            /* Stack top-bar actions below on very small screens if they wrap */
            .top-bar { flex-wrap: wrap; gap: 0.4rem; }
        }
    </style>
</head>
<body>

    <!-- ── Sidebar Overlay (mobile backdrop) ── -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <!-- ── Sidebar ── -->
    <nav class="sidebar" id="sidebar" aria-label="Main navigation">
        <div class="sidebar-brand">
            <h4><i class="bi bi-building"></i> Hostel Fees</h4>
            <small>Fee Management System</small>
        </div>
        <div class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <div class="nav-label">Management</div>
            <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                <i class="bi bi-door-open-fill"></i> Rooms
            </a>
            <a href="{{ route('persons.index') }}" class="nav-link {{ request()->routeIs('persons.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Persons
            </a>
            <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Fees
            </a>

            <div class="nav-label">Reports</div>
            <a href="{{ route('reports.monthly') }}" class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                <i class="bi bi-calendar-month"></i> Monthly Report
            </a>
            <a href="{{ route('reports.quarterly') }}" class="nav-link {{ request()->routeIs('reports.quarterly') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Quarterly Report
            </a>
            <a href="{{ route('reports.by_room') }}" class="nav-link {{ request()->routeIs('reports.by_room') ? 'active' : '' }}">
                <i class="bi bi-door-closed-fill"></i> Report by Room
            </a>
            <a href="{{ route('reports.by_person') }}" class="nav-link {{ request()->routeIs('reports.by_person') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Report by Person
            </a>
            <a href="{{ route('reports.deposit') }}" class="nav-link {{ request()->routeIs('reports.deposit') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Deposit Report
            </a>
        </div>

        <!-- Sidebar footer: user + logout -->
        <div class="sidebar-footer">
            <div class="user-info">
                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 w-100 text-start" style="background:none;cursor:pointer;color:#f87171;">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- ── Main Content ── -->
    <div class="main-content">
        <div class="top-bar no-print">
            <div class="d-flex align-items-center gap-2">
                <!-- Hamburger (mobile only) -->
                <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Open menu" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>
                <h5>@yield('page_title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-1 flex-wrap">
                @yield('top_actions')
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- ── Mobile Bottom Navigation ── -->
    <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
        <a href="{{ route('dashboard') }}"
           class="mob-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           aria-label="Dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('rooms.index') }}"
           class="mob-nav-item {{ request()->routeIs('rooms.*') ? 'active' : '' }}"
           aria-label="Rooms">
            <i class="bi bi-door-open-fill"></i>
            <span>Rooms</span>
        </a>
        <a href="{{ route('persons.index') }}"
           class="mob-nav-item {{ request()->routeIs('persons.*') ? 'active' : '' }}"
           aria-label="Persons">
            <i class="bi bi-people-fill"></i>
            <span>Persons</span>
        </a>
        <a href="{{ route('fees.index') }}"
           class="mob-nav-item {{ request()->routeIs('fees.*') ? 'active' : '' }}"
           aria-label="Fees">
            <i class="bi bi-cash-stack"></i>
            <span>Fees</span>
        </a>
        <button class="mob-nav-item" id="mobileMenuBtn" aria-label="More menu">
            <i class="bi bi-layout-sidebar"></i>
            <span>More</span>
        </button>
    </nav>

    <!-- ── Flash Toast Notifications ── -->
    @if(session('success') || $errors->any())
    <div class="flash-toast-wrap" id="flashToastWrap">
        @if(session('success'))
            <div class="flash-toast flash-success" role="alert">
                <div class="flash-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="flash-body">
                    <div class="flash-title">Success</div>
                    <div class="flash-message">{{ session('success') }}</div>
                </div>
                <button type="button" class="flash-close" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="flash-progress"><span></span></div>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-toast flash-error" role="alert">
                <div class="flash-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="flash-body">
                    <div class="flash-title">{{ $errors->count() > 1 ? 'Please fix the following' : 'Action blocked' }}</div>
                    <div class="flash-message">
                        @if($errors->count() === 1)
                            {{ $errors->first() }}
                        @else
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <button type="button" class="flash-close" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="flash-progress"><span></span></div>
            </div>
        @endif
    </div>
    @endif

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {

        /* ── Sidebar toggle ─────────────────────── */
        const sidebar      = document.getElementById('sidebar');
        const overlay      = document.getElementById('sidebarOverlay');
        const toggleBtn    = document.getElementById('sidebarToggle');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }

        if (toggleBtn)    toggleBtn.addEventListener('click',    () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openSidebar);
        if (overlay)      overlay.addEventListener('click',      closeSidebar);

        // Close sidebar when a nav link is tapped on mobile
        sidebar.querySelectorAll('a.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });

        /* ── Swipe gestures ─────────────────────── */
        let touchStartX = 0, touchStartY = 0;

        document.addEventListener('touchstart', e => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            const dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
            // Only horizontal swipes (dy < 60 prevents vertical scroll interference)
            if (dy > 60) return;
            // Swipe right from left edge → open
            if (touchStartX < 32 && dx > 55) openSidebar();
            // Swipe left on open sidebar → close
            if (dx < -55 && sidebar.classList.contains('show')) closeSidebar();
        }, { passive: true });

        /* ── Auto data-label for mobile table cards ── */
        document.querySelectorAll('.table-custom').forEach(function (table) {
            var headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
                return th.textContent.trim();
            });
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var cells = row.querySelectorAll('td');
                // Empty-state row (has a single td with colspan)
                if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
                    row.classList.add('mob-empty-row');
                    return;
                }
                cells.forEach(function (td, i) {
                    if (headers[i]) td.setAttribute('data-label', headers[i]);
                });
            });
        });

        /* ── Collapsible filter forms on mobile ─── */
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.card-custom.no-print').forEach(function (card) {
                var form = card.querySelector('form[method="GET"]');
                if (!form) return;

                // Check if any filter is currently active
                var hasActive = Array.from(form.querySelectorAll('input[type="text"], select')).some(function (el) {
                    return el.value && el.value.trim() !== '';
                });

                // Build the collapsible header
                var header = document.createElement('div');
                header.className = 'mob-filter-header';
                header.innerHTML =
                    '<span class="mob-filter-header-title">' +
                        '<i class="bi bi-funnel-fill" style="color:var(--primary);font-size:0.9rem;"></i>' +
                        ' Filters' +
                        (hasActive ? ' <span class="badge" style="background:var(--primary);font-size:0.6rem;padding:0.2rem 0.45rem;border-radius:20px;">Active</span>' : '') +
                    '</span>' +
                    '<button type="button" class="mob-filter-toggle-btn">' +
                        '<i class="bi bi-chevron-' + (hasActive ? 'up' : 'down') + '"></i>' +
                        '<span>' + (hasActive ? 'Hide' : 'Show') + '</span>' +
                    '</button>';

                card.insertBefore(header, form);

                // Collapse by default if no active filter
                if (!hasActive) form.style.display = 'none';

                header.addEventListener('click', function () {
                    var hidden = form.style.display === 'none';
                    form.style.display = hidden ? '' : 'none';
                    var icon = header.querySelector('.mob-filter-toggle-btn i');
                    var label = header.querySelector('.mob-filter-toggle-btn span');
                    if (icon)  icon.className  = 'bi bi-chevron-' + (hidden ? 'up' : 'down');
                    if (label) label.textContent = hidden ? 'Hide' : 'Show';
                });
            });
        }

        /* ── Flash toast auto-dismiss ───────────── */
        (function () {
            var wrap = document.getElementById('flashToastWrap');
            if (!wrap) return;

            function dismiss(toast) {
                if (!toast || toast.classList.contains('hide')) return;
                toast.classList.add('hide');
                toast.addEventListener('animationend', function () { toast.remove(); }, { once: true });
            }

            wrap.querySelectorAll('.flash-toast').forEach(function (toast, idx) {
                var closeBtn = toast.querySelector('.flash-close');
                if (closeBtn) closeBtn.addEventListener('click', function () { dismiss(toast); });

                var isError = toast.classList.contains('flash-error');
                var delay   = isError ? 7000 : 4000;
                var progress = toast.querySelector('.flash-progress > span');
                if (progress) progress.style.animationDuration = (delay / 1000) + 's';

                var timer = setTimeout(function () { dismiss(toast); }, delay);

                toast.addEventListener('mouseenter', function () {
                    clearTimeout(timer);
                    if (progress) progress.style.animationPlayState = 'paused';
                });
                toast.addEventListener('mouseleave', function () {
                    timer = setTimeout(function () { dismiss(toast); }, 2000);
                    if (progress) progress.style.animationPlayState = 'running';
                });
            });
        })();

    })();
    </script>
    @yield('scripts')
</body>
</html>
