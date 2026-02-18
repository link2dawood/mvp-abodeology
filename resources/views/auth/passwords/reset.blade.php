<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
        }

        body {
            margin: 0;
            background: var(--soft-grey);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
        }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

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

        .logo {
            margin-bottom: 25px;
            background: var(--black);
            padding: 20px;
            border-radius: 8px;
        }

        .logo img {
            width: 160px;
            height: auto;
            object-fit: contain;
        }

        h2 {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
        }

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

        input[type="email"][readonly] {
            background: #F5F5F5;
            color: #444;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--abodeology-teal);
        }

        input.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 15px;
            text-align: left;
        }

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

        .alert-error {
            background: #fee;
            border: 1px solid #dc3545;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #dc3545;
            font-size: 14px;
            text-align: left;
        }

        .alert-error ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }

        .footer-links a {
            color: var(--abodeology-teal);
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .wrapper {
                padding: 15px;
                align-items: flex-start;
                padding-top: 30px;
            }

            .login-box {
                padding: 24px 20px;
            }

            .logo img {
                width: 140px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="login-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
                <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px;">Abodeology®</span>
            </div>

            <h2>Reset your password</h2>

            @if ($errors->any())
                <div class="alert-error">
                    <strong>Error:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <input id="email"
                       type="email"
                       name="email"
                       value="{{ $email ?? old('email') }}"
                       placeholder="Email address"
                       readonly
                       autocomplete="email"
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input id="password"
                       type="password"
                       name="password"
                       placeholder="New password"
                       autocomplete="new-password"
                       autofocus
                       class="{{ $errors->has('password') ? 'error' : '' }}">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       placeholder="Confirm new password"
                       autocomplete="new-password">

                <button type="submit" class="btn">Reset password</button>
            </form>

            <div class="footer-links">
                Remember your password? <a href="{{ route('login') }}">Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
