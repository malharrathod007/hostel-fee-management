<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hostel Fee Manager</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/poppins.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary:      #4f46e5;
            --primary-dark: #3730a3;
            --primary-light:#818cf8;
            --sidebar-bg:   #1e1b4b;
            --bg:           #f1f5f9;
            --text:         #1e293b;
            --text-muted:   #64748b;
        }

        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* subtle animated gradient background */
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(79,70,229,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(129,140,248,0.07) 0%, transparent 50%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1.25rem;
        }

        /* ── Card ─────────────────────────────── */
        .login-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 16px 48px rgba(15, 23, 42, 0.1), 0 2px 8px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        /* ── Header ───────────────────────────── */
        .login-header {
            background: var(--sidebar-bg);
            padding: 2.25rem 2rem 1.75rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* decorative blobs */
        .login-header::before,
        .login-header::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.12;
        }
        .login-header::before {
            width: 160px; height: 160px;
            background: var(--primary-light);
            top: -60px; left: -40px;
        }
        .login-header::after {
            width: 120px; height: 120px;
            background: var(--primary-light);
            bottom: -50px; right: -30px;
        }

        .login-header .icon-circle {
            width: 66px;
            height: 66px;
            background: rgba(255,255,255,0.12);
            border: 2px solid rgba(255,255,255,0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.1rem;
            font-size: 1.65rem;
            color: var(--primary-light);
            position: relative;
            z-index: 1;
        }

        .login-header h3 {
            color: #fff;
            margin: 0 0 0.3rem;
            font-weight: 700;
            font-size: 1.3rem;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            color: #c7d2fe;
            margin: 0;
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        /* ── Body ─────────────────────────────── */
        .login-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .form-control {
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .input-group-text {
            background: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 10px 0 0 10px;
            color: var(--text-muted);
            min-width: 42px;
            justify-content: center;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-light);
            color: var(--primary);
        }

        /* ── Password toggle ──────────────────── */
        .pwd-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            font-size: 1rem;
            z-index: 5;
            transition: color 0.15s;
        }
        .pwd-toggle:hover { color: var(--primary); }
        .input-group.has-pwd-toggle .form-control { padding-right: 2.5rem; }

        /* ── Button ───────────────────────────── */
        .btn-login {
            background: var(--primary);
            border: none;
            color: #fff;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(79,70,229,0.3);
        }

        .btn-login:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79,70,229,0.35);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(79,70,229,0.25);
        }

        /* ── Alert ────────────────────────────── */
        .alert-danger {
            border-radius: 10px;
            font-size: 0.85rem;
            border: none;
            background: #fef2f2;
            color: #991b1b;
            padding: 0.75rem 1rem;
        }

        /* ── Divider ──────────────────────────── */
        .form-check-label { font-size: 0.83rem; color: var(--text-muted); }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }

        /* ── Mobile adjustments ───────────────── */
        @media (max-width: 480px) {
            .login-wrapper { padding: 0.75rem; }

            .login-header {
                padding: 1.75rem 1.5rem 1.4rem;
            }
            .login-header .icon-circle {
                width: 58px; height: 58px; font-size: 1.4rem;
            }
            .login-header h3 { font-size: 1.15rem; }

            .login-body { padding: 1.5rem 1.25rem; }

            .btn-login { padding: 0.7rem; font-size: 0.9rem; }
        }

        @media (max-width: 360px) {
            .login-wrapper { padding: 0.5rem; }
            .login-body { padding: 1.25rem 1rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Header -->
            <div class="login-header">
                <div class="icon-circle">
                    <i class="bi bi-building"></i>
                </div>
                <h3>Hostel Fee Manager</h3>
                <p>Sign in to your account</p>
            </div>

            <!-- Body -->
            <div class="login-body">

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="admin@hostel.com"
                                   required
                                   autofocus
                                   autocomplete="email">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group has-pwd-toggle" style="position:relative;">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="pwd-toggle" id="pwdToggle" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="pwdToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password show/hide toggle
        var pwdInput  = document.getElementById('password');
        var pwdToggle = document.getElementById('pwdToggle');
        var pwdIcon   = document.getElementById('pwdToggleIcon');

        if (pwdToggle) {
            pwdToggle.addEventListener('click', function () {
                var isText = pwdInput.type === 'text';
                pwdInput.type = isText ? 'password' : 'text';
                pwdIcon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        }
    </script>
</body>
</html>
