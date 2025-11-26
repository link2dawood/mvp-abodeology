<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* BRAND COLOURS */
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
        }

        /* GLOBAL */
        body {
            margin: 0;
            background: var(--soft-grey);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
        }

        /* CENTER WRAPPER */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        /* LOGIN BOX */
        .login-box {
            background: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.07);
            text-align: center;
        }

        /* LOGO */
        .logo {
            margin-bottom: 25px;
            background: var(--black);
            padding: 20px;
            border-radius: 8px;
        }

        .logo img {
            width: 160px;
        }

        /* HEADING */
        h2 {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        /* FORM */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #D9D9D9;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--abodeology-teal);
        }

        input.error {
            border-color: #dc3545;
        }

        /* ERROR MESSAGES */
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 15px;
            text-align: left;
        }

        /* BUTTON */
        .btn {
            width: 100%;
            background: var(--abodeology-teal);
            color: var(--white);
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #25A29F;
        }

        .btn:active {
            transform: scale(0.98);
        }

        /* LINKS */
        a {
            color: var(--abodeology-teal);
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer-links {
            margin-top: 20px;
        }

        .footer-links p {
            margin: 10px 0;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 480px) {
            .wrapper {
                padding: 15px;
                align-items: flex-start;
                padding-top: 30px;
            }

            .login-box {
                padding: 25px 20px;
                max-width: 100%;
            }

            .logo img {
                width: 140px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="email"],
            input[type="password"] {
                padding: 12px;
                font-size: 14px;
            }

            .btn {
                padding: 12px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="login-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" height="40" style="max-height: 40px;" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
                <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px;">Abodeology®</span>
            </div>
            
            <h2>Sign in to Abodeology</h2>
            
            @if ($errors->any())
                <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px; text-align: left;">
                    <strong>Error:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <input type="email" 
                       name="email" 
                       placeholder="Email address" 
                       value="{{ old('email') }}"
                       required 
                       autofocus
                       class="{{ $errors->has('email') ? 'error' : '' }}"
                       autocomplete="email">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" 
                       name="password" 
                       placeholder="Password" 
                       required
                       class="{{ $errors->has('password') ? 'error' : '' }}"
                       autocomplete="current-password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn">Log In</button>
            </form>

            <div class="footer-links">
                <p><a href="{{ route('password.request') }}">Forgot your password?</a></p>
                <p>Don't have an account? <a href="{{ route('register') }}">Create one</a></p>
            </div>
        </div>
    </div>
</body>
</html>