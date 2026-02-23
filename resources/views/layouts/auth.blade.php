<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    
    <!-- Modern Design System -->
    <link href="{{ asset('css/modern-design.css') }}" rel="stylesheet"/>
    
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            font-family: Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
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
        
        .page {
            background: var(--bg-secondary);
            min-height: 100vh;
        }
        
        .card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        
        .card:hover {
            border-color: var(--accent-color);
        }
        
        .form-control {
            background: var(--bg-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-sm) !important;
            transition: var(--transition) !important;
        }
        
        .form-control:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1) !important;
        }
        
        .btn-primary {
            background: var(--accent-color) !important;
            border: 1px solid var(--accent-color) !important;
            border-radius: var(--border-radius-sm) !important;
            transition: var(--transition) !important;
        }
        
        .btn-primary:hover {
            background: var(--accent-dark) !important;
            border-color: var(--accent-dark) !important;
        }
        
        .btn-white {
            background: var(--bg-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-sm) !important;
            color: var(--text-primary) !important;
            transition: var(--transition) !important;
        }
        
        .btn-white:hover {
            background: var(--bg-tertiary) !important;
            border-color: var(--accent-color) !important;
            color: var(--text-primary) !important;
        }
        
        .btn:active {
            transform: scale(0.98);
        }
        
        .alert {
            background: var(--bg-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-sm) !important;
        }
        
        .text-white {
            color: var(--text-primary) !important;
        }
        
        .text-white-50 {
            color: var(--text-secondary) !important;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .page table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }

            .page .btn,
            .page button,
            .page input[type="submit"],
            .page input[type="button"] {
                max-width: 100%;
                box-sizing: border-box;
            }

            .page input:not([type="checkbox"]):not([type="radio"]),
            .page select,
            .page textarea {
                max-width: 100%;
            }

            header {
                padding: 12px 0;
            }

            header img {
                height: 35px !important;
                max-height: 35px !important;
            }

            .container {
                padding: 20px 15px !important;
                max-width: 100% !important;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 10px 0;
            }

            header img {
                height: 30px !important;
                max-height: 30px !important;
            }

            .container {
                padding: 15px 12px !important;
            }
        }
    </style>
</head>
<body class="d-flex flex-column">
    <header style="background: #0F0F0F; padding: 15px 0; text-align: center;">
        <a href="{{ url('/') }}" style="display: inline-block;">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" height="40" style="max-height: 40px;" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
            <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px; line-height: 40px;">Abodeology®</span>
        </a>
    </header>
    <div class="page page-center">
        <div class="container py-4" style="max-width: 460px; margin: 0 auto;">
            @yield('content')
        </div>
    </div>
</body>
</html>