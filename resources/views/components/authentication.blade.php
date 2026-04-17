<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset('app-assets/images/logo-sm.png') }}">
    <link href="{{ asset('app-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root{
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --panel: #ffffff;
            --bg: #f8fafc;
            --success: #16a34a;
            --danger: #dc2626;
        }

        html, body {
            min-height: 100%;
            font-family: 'Inter', sans-serif;
            background: #eef4fb;
        }

        body {
            margin: 0;
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 28%),
                radial-gradient(circle at bottom right, rgba(14,165,233,.08), transparent 30%),
                #eef4fb;
            padding: 20px;
        }

        .auth-frame {
            width: 100%;
            min-height: calc(100vh - 40px);
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(15, 23, 42, 0.12);
        }

        /* LEFT */
        .auth-visual {
            position: relative;
            padding: 42px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                linear-gradient(135deg, rgba(15,23,42,.76), rgba(37,99,235,.68)),
                url('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        .auth-visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to bottom right, rgba(15,23,42,.20), rgba(15,23,42,.45));
            pointer-events: none;
        }

        .auth-visual > * {
            position: relative;
            z-index: 1;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .auth-brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 12px;
            background: rgba(255,255,255,.12);
            padding: 6px;
        }

        .auth-brand-text h2 {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -.02em;
        }

        .auth-brand-text p {
            margin: 3px 0 0;
            font-size: .82rem;
            color: rgba(255,255,255,.82);
        }

        .auth-hero {
            max-width: 580px;
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            font-size: .78rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .auth-hero h1 {
            font-size: 3rem;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -.04em;
            margin-bottom: 16px;
        }

        .auth-hero p {
            font-size: 1rem;
            line-height: 1.8;
            color: rgba(255,255,255,.88);
            max-width: 540px;
            margin-bottom: 28px;
        }

        .auth-audience {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .auth-audience-item {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(8px);
            font-size: .84rem;
            font-weight: 600;
        }

        .auth-audience-item i {
            font-size: 1rem;
        }

        .auth-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            max-width: 720px;
        }

        .auth-stat {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 18px 18px 16px;
        }

        .auth-stat-value {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .auth-stat-label {
            font-size: .82rem;
            line-height: 1.5;
            color: rgba(255,255,255,.82);
        }

        /* RIGHT */
        .auth-panel {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
        }

        .auth-panel-inner {
            width: 100%;
            max-width: 420px;
        }

        .auth-mobile-brand {
            display: none;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .auth-mobile-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.03em;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: .95rem;
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .auth-alert {
            border-radius: 14px;
            padding: 13px 15px;
            font-size: .84rem;
            margin-bottom: 18px;
            border: 1px solid transparent;
        }

        .auth-alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .auth-alert-success {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .auth-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .auth-group {
            margin-bottom: 18px;
        }

        .auth-label {
            display: block;
            margin-bottom: 8px;
            font-size: .8rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input {
            width: 100%;
            height: 50px;
            border: 1px solid var(--border);
            background: #f8fafc;
            border-radius: 14px;
            padding: 0 16px;
            color: #0f172a;
            font-size: .92rem;
            outline: none;
            transition: .2s ease;
        }

        .auth-input:focus {
            border-color: #93c5fd;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37,99,235,.08);
        }

        .auth-input.is-invalid {
            border-color: #f87171;
        }

        .auth-input::placeholder {
            color: #94a3b8;
        }

        .auth-input-wrap .auth-input {
            padding-right: 48px;
        }

        .auth-eye {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 1.15rem;
            transition: .2s ease;
        }

        .auth-eye:hover {
            color: var(--primary);
        }

        .invalid-feedback {
            display: block;
            margin-top: 6px;
            font-size: .77rem;
            color: var(--danger);
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: .88rem;
            color: #475569;
            cursor: pointer;
            margin: 0;
        }

        .auth-check input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            flex-shrink: 0;
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-size: .84rem;
            font-weight: 700;
        }

        .auth-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .auth-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            font-size: .95rem;
            font-weight: 800;
            letter-spacing: .01em;
            box-shadow: 0 12px 24px rgba(37,99,235,.24);
            transition: .2s ease;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(37,99,235,.28);
        }

        .auth-btn:active {
            transform: translateY(0);
        }

        .auth-note {
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: .84rem;
            line-height: 1.7;
        }

        .auth-footer {
            margin-top: 26px;
            text-align: center;
            font-size: .75rem;
            color: #94a3b8;
        }

        @media (max-width: 1199px) {
            .auth-frame {
                grid-template-columns: 1fr 460px;
            }

            .auth-hero h1 {
                font-size: 2.45rem;
            }
        }

        @media (max-width: 991px) {
            .auth-shell {
                padding: 0;
            }

            .auth-frame {
                min-height: 100vh;
                border-radius: 0;
                grid-template-columns: 1fr;
            }

            .auth-visual {
                display: none;
            }

            .auth-mobile-brand {
                display: flex;
            }

            .auth-panel {
                padding: 30px 22px;
            }

            .auth-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-frame">

            <div class="auth-visual">
                <div class="auth-brand">
                    <img src="{{ asset('app-assets/images/logo-sm.png') }}" alt="{{ config('app.name') }}">
                    <div class="auth-brand-text">
                        <h2>{{ config('app.name') }}</h2>
                        <p>Student, Parent & Staff Portal</p>
                    </div>
                </div>

                <div class="auth-hero">
                    <div class="auth-kicker">
                        <i class="mdi mdi-school-outline"></i>
                        Connected school experience
                    </div>

                    <h1>
                        One secure portal for students, parents, and staff.
                    </h1>

                    <p>
                        Access academic updates, communication, school services, and daily operations from one modern platform built for the whole school community.
                    </p>

                    <div class="auth-audience">
                        <div class="auth-audience-item">
                            <i class="mdi mdi-account-school-outline"></i>
                            Students
                        </div>
                        <div class="auth-audience-item">
                            <i class="mdi mdi-account-group-outline"></i>
                            Parents
                        </div>
                        <div class="auth-audience-item">
                            <i class="mdi mdi-briefcase-account-outline"></i>
                            Staff
                        </div>
                    </div>
                </div>

                <div class="auth-stats">
                    <div class="auth-stat">
                        <div class="auth-stat-value">Secure</div>
                        <div class="auth-stat-label">Protected access for every school account and role.</div>
                    </div>
                    <div class="auth-stat">
                        <div class="auth-stat-value">Unified</div>
                        <div class="auth-stat-label">A single place for learning, communication, and administration.</div>
                    </div>
                    <div class="auth-stat">
                        <div class="auth-stat-value">Reliable</div>
                        <div class="auth-stat-label">Built to support day-to-day school operations with ease.</div>
                    </div>
                </div>
            </div>

            <div class="auth-panel">
                <div class="auth-panel-inner">

                    <div class="auth-mobile-brand">
                        <img src="{{ asset('app-assets/images/logo-sm.png') }}" alt="{{ config('app.name') }}">
                        <div>
                            <div style="font-weight:800;color:#0f172a;">{{ config('app.name') }}</div>
                            <div style="font-size:.8rem;color:#64748b;">Student, Parent & Staff Portal</div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="auth-alert auth-alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('status'))
                        <div class="auth-alert auth-alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="auth-alert auth-alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('form')

                    <div class="auth-footer">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('app-assets/js/vendor.min.js') }}"></script>
</body>
</html>