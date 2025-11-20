<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Buyer Dashboard') | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* BRAND STYLE */
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
            --line-grey: #EAEAEA;
        }

        /* GLOBAL */
        body {
            margin: 0;
            background: var(--soft-grey);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVIGATION */
        .navbar {
            background: var(--black);
            padding: 15px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- NAVIGATION HEADER -->
    <header class="navbar">
        <a href="{{ route('buyer.dashboard') }}">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'">
        </a>
        <nav class="nav-links">
            <a href="{{ route('buyer.dashboard') }}">Dashboard</a>
            <a href="{{ route('buyer.profile') }}">My Profile</a>
            <a href="#">Notifications</a>
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
            <a href="#">Contact Support</a>
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
