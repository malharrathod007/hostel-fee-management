<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hostel Fee Manager')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' rx='3' fill='%234f46e5'/><text x='2' y='13' font-size='13' font-family='sans-serif'>🏨</text></svg>">
    <!-- Bootstrap 5 -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <!-- Poppins font -->
    <link href="{{ asset('css/poppins.css') }}" rel="stylesheet">
    <!-- Design system -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <!-- ── Sidebar Overlay (mobile backdrop) ── -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <!-- ── Sidebar ── -->
    <nav class="sidebar" id="sidebar" aria-label="Main navigation">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-building"></i></div>
            <div>
                <h4>Hostel Fees</h4>
                <small>Fee Management System</small>
            </div>
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
                <button type="submit" class="logout-link">
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

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
