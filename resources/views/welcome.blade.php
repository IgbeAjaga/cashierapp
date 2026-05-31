<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashierPro - Daily Cash & Invoice Management</title>

    <style>
        :root {
            --primary: #1e40af;     /* blue-700 */
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #eab308;      /* yellow-500 - for money feel */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(to bottom, var(--gray-50), var(--white));
            color: var(--gray-900);
            line-height: 1.5;
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        @media (min-width: 1024px) {
            .container { padding-left: 2rem; padding-right: 2rem; }
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.875rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-link {
            color: var(--gray-700);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-outline:hover {
            background: rgba(30, 64, 175, 0.05);
        }

        section.hero {
            padding-top: 8rem;
            padding-bottom: 5rem;
            background: linear-gradient(to bottom right, #dbeafe, white, #fefce8);
            text-align: center;
        }

        h1 {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 640px) { h1 { font-size: 3.75rem; } }
        @media (min-width: 768px)  { h1 { font-size: 4.5rem; } }
        @media (min-width: 1024px) { h1 { font-size: 5rem; } }

        .lead {
            font-size: 1.25rem;
            color: var(--gray-600);
            max-width: 48rem;
            margin: 0 auto 2.5rem;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            justify-content: center;
            max-width: 28rem;
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .buttons { flex-direction: row; }
        }

        .feature-grid {
            display: grid;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .feature-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            transform: translateY(-4px);
        }

        .icon-circle {
            width: 3.5rem;
            height: 3.5rem;
            background: var(--gray-100);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .cta-section {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            color: white;
            padding: 5rem 0;
            text-align: center;
        }

        footer {
            background: var(--gray-900);
            color: #d1d5db;
            padding: 3rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="container">
        <div class="nav-inner">
            <a href="/" class="logo">CashierPro</a>

            <div style="display: flex; align-items: center; gap: 1.5rem;">
                @if(auth()->check())
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('invoice-reports.index') }}" class="btn-primary">Enter Sale</a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>Accurate Daily Cash.<br><span style="color: var(--primary);">Zero Reconciliation Stress.</span></h1>

        <p class="lead">
            Record sales, track payments, manage shifts, and balance your books effortlessly.<br>
            Built for cashiers who want speed and accuracy.
        </p>

        <div class="buttons">
            @if(auth()->check())
                <a href="{{ route('invoice-reports.index') }}" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.125rem;">
                    New Entry
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.125rem;">
                    Login to Dashboard
                </a>
            @endif
        </div>

        <p style="margin-top: 2rem; color: var(--gray-500); font-size: 0.9rem;">
            Invoice Reports • Awaiting Payments • Online Transactions • Shift Management
        </p>
    </div>
</section>

<!-- FEATURES -->
<section style="padding: 5rem 0; background: white;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 2.25rem; font-weight: 700;">Everything you need to run a clean cash desk</h2>
        </div>

        <div class="feature-grid">

            <div class="card">
                <div class="icon-circle" style="background: #dbeafe;">📊</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Invoice Reports</h3>
                <p style="color: var(--gray-600);">
                    Record complete sales with multiple payment methods and automatic balance calculation.
                </p>
            </div>

            <div class="card">
                <div class="icon-circle" style="background: #fef3c7;">⏳</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Awaiting Reports</h3>
                <p style="color: var(--gray-600);">
                    Track pending invoices and payments with clear reconciliation.
                </p>
            </div>

            <div class="card">
                <div class="icon-circle" style="background: #ecfdf5;">🌐</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Online Transactions</h3>
                <p style="color: var(--gray-600);">
                    Manage POS, transfers, cheques and online payments in one place.
                </p>
            </div>

            <div class="card">
                <div class="icon-circle" style="background: #f3e8ff;">🔄</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Shift Management</h3>
                <p style="color: var(--gray-600);">
                    Morning, Afternoon & Night shifts with full accountability and audit trail.
                </p>
            </div>

            <div class="card">
                <div class="icon-circle" style="background: #fee2e2;">📋</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Real-time Balancing</h3>
                <p style="color: var(--gray-600);">
                    Automatic difference calculation and visual alerts for unbalanced entries.
                </p>
            </div>

            <div class="card">
                <div class="icon-circle" style="background: #ecfdf5;">👤</div>
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem;">Full Audit Trail</h3>
                <p style="color: var(--gray-600);">
                    Know who recorded or edited every entry and when.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">
            Ready to simplify your daily cash operations?
        </h2>
        <p style="font-size: 1.25rem; max-width: 600px; margin: 0 auto 2rem;">
            Join other cashiers who trust CashierPro for accurate and stress-free daily reporting.
        </p>
        @if(auth()->check())
            <a href="{{ route('invoice-reports.index') }}" class="btn-primary" style="font-size: 1.25rem; padding: 1rem 3rem;">
                Start Recording Sales Invoice
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-primary" style="font-size: 1.25rem; padding: 1rem 3rem;">
                Login Now
            </a>
        @endif
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p style="font-size: 1.35rem; font-weight: 600; color: white; margin-bottom: 0.5rem;">CashierPro</p>
        <p style="margin-bottom: 1.5rem;">© {{ date('Y') }} All Rights Reserved.</p>

        <div style="display: flex; justify-content: center; gap: 2rem; font-size: 0.9rem;">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Support</a>
        </div>
    </div>
</footer>

</body>
</html>