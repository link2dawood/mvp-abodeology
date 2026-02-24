<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard') | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* BRAND */
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
            --line-grey: #EAEAEA;
            --danger: #E14F4F;
        }

        /* GLOBAL */
        body {
            margin: 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: var(--soft-grey);
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        img,
        svg,
        video,
        canvas {
            max-width: 100%;
            height: auto;
        }

        /* NAVBAR - sticky so always in view */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: var(--black);
            padding: 15px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .navbar img {
            height: 40px;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            color: var(--white);
            margin-left: 28px;
            text-decoration: none;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--abodeology-teal);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            overflow-x: hidden;
        }

        /* PAGE CONTAINER */
        .container {
            max-width: 1400px;
            margin: 35px auto;
            padding: 0 22px;
        }

        /* FOOTER */
        .footer {
            background: var(--black);
            color: var(--white);
            padding: 25px 35px;
            text-align: center;
            margin-top: 50px;
            flex-shrink: 0;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #999;
        }

        .footer a {
            color: var(--abodeology-teal);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .main-content table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }

            .main-content table tbody,
            .main-content table thead,
            .main-content table tr {
                white-space: nowrap;
            }

            .main-content .btn,
            .main-content button,
            .main-content input[type="submit"],
            .main-content input[type="button"] {
                max-width: 100%;
                box-sizing: border-box;
            }

            .main-content input:not([type="checkbox"]):not([type="radio"]),
            .main-content select,
            .main-content textarea {
                max-width: 100%;
            }

            .main-content [style*="display: flex"] {
                flex-wrap: wrap !important;
                gap: 10px;
            }

            .main-content [style*="display: flex"] > * {
                max-width: 100%;
            }

            .navbar {
                padding: 12px 20px;
                flex-wrap: wrap;
            }

            .navbar img {
                height: 35px;
            }

            .nav-links {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                margin-top: 15px;
                display: none;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                margin: 8px 0;
                margin-left: 0;
                padding: 8px 0;
                width: 100%;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .nav-links form {
                width: 100%;
                margin-left: 0;
            }

            .nav-links form a {
                margin: 8px 0;
            }

            .mobile-menu-toggle {
                display: block;
                background: none;
                border: none;
                color: var(--white);
                font-size: 24px;
                cursor: pointer;
                padding: 5px;
            }

            .container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .footer {
                padding: 20px 15px;
                font-size: 12px;
            }

            .footer p {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px 15px;
            }

            .navbar img {
                height: 30px;
            }

            .container {
                padding: 0 12px;
                margin: 15px auto;
            }
        }

        .mobile-menu-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- NAVBAR HEADER -->
    <header class="navbar">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" height="40" style="max-height: 40px;" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
            <span style="display: none; color: var(--abodeology-teal); font-weight: 600; font-size: 20px; line-height: 40px;">Abodeology®</span>
        </a>
        <button class="mobile-menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')" aria-label="Toggle menu">☰</button>
        <nav class="nav-links">
            @if(auth()->user()->role === 'agent')
                <a href="{{ route('admin.agent.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            @endif
            <a href="{{ route('admin.valuations.index') }}">Valuations</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.index') }}">Users</a>
            @endif
            <a href="{{ route('admin.properties.index') }}">Properties</a>
            <a href="{{ route('admin.homechecks.index') }}">HomeChecks</a>
            <a href="{{ route('admin.aml-checks.index') }}">AML Checks</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.email-templates.index') }}">Email Templates</a>
            @endif
            <a href="{{ route('profile.show') }}">Profile</a>
            <a href="{{ route('admin.notifications') }}">Notifications</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.settings.index') }}">Settings</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 28px;">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" style="margin-left: 0;">Logout</a>
            </form>
        </nav>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} Abodeology®. All rights reserved.</p>
        <p>
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a> | 
            <a href="mailto:support@abodeology.co.uk">Contact Support</a>
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
