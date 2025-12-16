<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Seller Dashboard') | Abodeology®</title>
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
            width: auto;
            object-fit: contain;
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

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 20px;
                flex-wrap: wrap;
            }

            .navbar img {
                height: 35px;
                width: auto;
                object-fit: contain;
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

            .nav-links a[style*="background: rgba(76, 175, 80"] {
                margin-bottom: 12px;
                padding: 10px;
                text-align: center;
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
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px 15px;
            }

            .navbar img {
                height: 30px;
                width: auto;
                object-fit: contain;
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
    
    @stack('styles')
</head>
<body>
    <!-- NAVIGATION HEADER -->
    <header class="navbar">
        <a href="{{ route('seller.dashboard') }}">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
            <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 20px; line-height: 40px;">Abodeology®</span>
        </a>
        <button class="mobile-menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')" aria-label="Toggle menu">☰</button>
        <nav class="nav-links">
            @if(auth()->user()->role === 'both')
                <a href="{{ route('buyer.dashboard') }}" style="background: rgba(76, 175, 80, 0.2); padding: 6px 12px; border-radius: 4px; margin-right: 10px; border: 1px solid #4CAF50;">
                    <span style="color: #4CAF50;">🔄 Switch to Buyer</span>
                </a>
            @endif
            <a href="{{ route('seller.dashboard') }}">Dashboard</a>
            <a href="{{ route('seller.properties.index') }}">Properties</a>
            <a href="{{ route('profile.show') }}">Profile</a>
            <a href="{{ route('seller.notifications') }}">Notifications</a>
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
