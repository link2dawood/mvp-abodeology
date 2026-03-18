<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thanks for signing up | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --dark-text: #1E1E1E;
        }

        body {
            margin: 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 24px 16px;
            box-sizing: border-box;
        }

        .card {
            background: var(--white);
            width: 100%;
            max-width: 460px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.07);
            text-align: center;
        }

        .logo {
            margin-bottom: 20px;
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
            margin: 0 0 12px 0;
            font-size: 26px;
            font-weight: 600;
        }

        .subtext {
            font-size: 15px;
            color: #555;
            margin: 0 0 22px 0;
            line-height: 1.5;
        }

        .alert {
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
            text-align: left;
        }

        .alert-success {
            background: #E7F7F6;
            border: 1px solid rgba(44, 184, 180, 0.35);
            color: #0b4a49;
        }

        .btn {
            width: 100%;
            background: var(--abodeology-teal);
            color: var(--white);
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.25s ease;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }

        .btn:hover {
            background: #239491;
        }

        .btn-secondary {
            margin-top: 12px;
            background: #F4F4F4;
            color: #1E1E1E;
        }

        .btn-secondary:hover {
            background: #EAEAEA;
        }

        .link {
            color: var(--abodeology-teal);
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        .fineprint {
            margin-top: 16px;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            text-align: left;
        }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $dashboardUrl = null;

    if ($user) {
        $dashboardUrl = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'agent' => route('admin.agent.dashboard'),
            'buyer' => route('buyer.dashboard'),
            'seller' => route('seller.dashboard'),
            'both' => route('combined.dashboard'),
            'pva' => route('pva.dashboard'),
            default => route('login'),
        };
    }
@endphp

    <div class="wrapper">
        <div class="card">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo">
            </div>

            <h2>Thank you for signing up</h2>
            <p class="subtext">
                We’ve sent a verification email to <strong>{{ $user?->email }}</strong>.
                Please click the link in that email to verify your account.
            </p>

            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn">Resend verification email</button>
            </form>

            @if ($dashboardUrl)
                <a class="btn btn-secondary" href="{{ $dashboardUrl }}" target="_blank" rel="noopener noreferrer">Go to dashboard</a>
            @endif

            <div class="fineprint">
                If you don’t see the email, check your spam/junk folder, then click “Resend verification email” above.
            </div>
        </div>
    </div>
</body>
</html>
