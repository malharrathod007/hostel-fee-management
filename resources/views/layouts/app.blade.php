<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hostel Fee Manager')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
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

        /* Main content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .top-bar {
            background: var(--card-bg);
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text);
        }

        .content-area {
            padding: 1.5rem 2rem;
        }

        /* Cards */
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

        /* Table */
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

        /* Badges */
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-partial { background: #dbeafe; color: #1e40af; }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Form */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Flash toast notifications */
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

        .flash-toast.flash-success .flash-icon {
            background: #d1fae5;
            color: var(--success);
        }
        .flash-toast.flash-error .flash-icon {
            background: #fee2e2;
            color: var(--danger);
        }

        .flash-toast .flash-body {
            flex: 1;
            min-width: 0;
        }

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

        .flash-toast .flash-message ul {
            margin: 0;
            padding-left: 1.1rem;
        }

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

        .flash-toast .flash-close:hover {
            background: #f1f5f9;
            color: var(--text);
        }

        .flash-toast .flash-progress {
            position: absolute;
            left: 0;
            bottom: 0;
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

        .flash-toast.hide {
            animation: flashSlideOut 0.3s ease forwards;
        }

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
            .flash-toast .flash-icon {
                width: 34px;
                height: 34px;
                font-size: 1rem;
            }
            .flash-toast .flash-title { font-size: 0.85rem; }
            .flash-toast .flash-message { font-size: 0.78rem; }
            @keyframes flashSlideIn {
                0%   { opacity: 0; transform: translateY(-120%); }
                100% { opacity: 1; transform: translateY(0); }
            }
            @keyframes flashSlideOut {
                0%   { opacity: 1; transform: translateY(0); max-height: 200px; margin-bottom: 0.75rem; }
                100% { opacity: 0; transform: translateY(-120%); max-height: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; border-width: 0; }
            }
        }

        /* Print */
        @media print {
            .sidebar, .top-bar, .no-print { display: none !important; }
            .main-content { margin-left: 0; }
            .content-area { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-building"></i> Hostel Fee</h4>
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
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar no-print">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="d-inline">@yield('page_title', 'Dashboard')</h5>
            </div>
            <div>
                @yield('top_actions')
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Flash Toast Notifications -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const wrap = document.getElementById('flashToastWrap');
            if (!wrap) return;

            function dismiss(toast) {
                if (!toast || toast.classList.contains('hide')) return;
                toast.classList.add('hide');
                toast.addEventListener('animationend', () => toast.remove(), { once: true });
            }

            wrap.querySelectorAll('.flash-toast').forEach((toast, idx) => {
                const closeBtn = toast.querySelector('.flash-close');
                if (closeBtn) closeBtn.addEventListener('click', () => dismiss(toast));

                // Auto-dismiss: success after 4s, errors after 7s (more reading time)
                const isError = toast.classList.contains('flash-error');
                const delay = isError ? 7000 : 4000;
                const progress = toast.querySelector('.flash-progress > span');
                if (progress) progress.style.animationDuration = (delay / 1000) + 's';

                let timer = setTimeout(() => dismiss(toast), delay);

                // Pause on hover (desktop)
                toast.addEventListener('mouseenter', () => {
                    clearTimeout(timer);
                    if (progress) progress.style.animationPlayState = 'paused';
                });
                toast.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => dismiss(toast), 2000);
                    if (progress) progress.style.animationPlayState = 'running';
                });
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
