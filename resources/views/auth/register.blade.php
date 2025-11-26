<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Abodeology®</title>
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

        /* GLOBAL STYLE */
        body {
            margin: 0;
            background: var(--soft-grey);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
            min-height: 100vh;
        }

        /* WRAPPER */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px 20px;
            min-height: 100vh;
        }

        /* CARD */
        .register-box {
            background: var(--white);
            width: 100%;
            max-width: 460px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.07);
            text-align: center;
        }

        /* LOGO */
        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            width: 160px;
        }

        /* HEADINGS */
        h2 {
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: 600;
        }

        .subtext {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
        }

        /* FORM FIELDS */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        select {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #D9D9D9;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
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

        /* FOOTER LINKS */
        .footer-links {
            margin-top: 20px;
        }

        .footer-links a {
            color: var(--abodeology-teal);
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .footer-links p {
            margin: 10px 0;
        }

        /* RADIO BUTTON GROUP */
        .role-box {
            background: var(--soft-grey);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid #E5E5E5;
        }

        .role-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .role-option {
            margin-bottom: 8px;
        }

        .role-option label {
            margin-left: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .role-option input[type="radio"] {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 480px) {
            .wrapper {
                padding: 15px;
                align-items: flex-start;
                padding-top: 30px;
            }

            .register-box {
                padding: 25px 20px;
                max-width: 100%;
            }

            .logo img {
                width: 140px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            select {
                padding: 12px;
                font-size: 14px;
            }

            .btn {
                padding: 12px;
                font-size: 15px;
            }

            .role-box {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="register-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.svg') }}" alt="Abodeology Logo" height="40" style="max-height: 40px;" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.svg') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
                <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px;">Abodeology®</span>
            </div>
            
            <h2>Create your account</h2>
            <p class="subtext">Join Abodeology to manage your property journey.</p>
            
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

            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <input type="text" 
                       name="name" 
                       placeholder="Full name" 
                       value="{{ old('name') }}"
                       required 
                       autofocus
                       class="{{ $errors->has('name') ? 'error' : '' }}"
                       autocomplete="name">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="email" 
                       name="email" 
                       placeholder="Email address" 
                       value="{{ old('email') }}"
                       required
                       class="{{ $errors->has('email') ? 'error' : '' }}"
                       autocomplete="email">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="tel" 
                       name="phone" 
                       placeholder="Mobile number" 
                       value="{{ old('phone') }}"
                       required
                       class="{{ $errors->has('phone') ? 'error' : '' }}"
                       autocomplete="tel">
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" 
                       name="password" 
                       placeholder="Create password" 
                       required
                       class="{{ $errors->has('password') ? 'error' : '' }}"
                       autocomplete="new-password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" 
                       name="password_confirmation" 
                       placeholder="Confirm password" 
                       required
                       autocomplete="new-password">

                <!-- ACCOUNT ROLE SELECTION -->
                <div class="role-box {{ $errors->has('role') ? 'error' : '' }}" style="{{ $errors->has('role') ? 'border-color: #dc3545;' : '' }}">
                    <div class="role-title">I am registering as:</div>
                    <div class="role-option">
                        <input type="radio" id="buyer" name="role" value="buyer" {{ old('role') == 'buyer' ? 'checked' : '' }} required>
                        <label for="buyer">Buyer</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="seller" name="role" value="seller" {{ old('role') == 'seller' ? 'checked' : '' }}>
                        <label for="seller">Seller</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="both" name="role" value="both" {{ old('role') == 'both' ? 'checked' : '' }}>
                        <label for="both">Both Buyer & Seller</label>
                    </div>
                </div>
                @error('role')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn">Create Account</button>
            </form>

            <div class="footer-links">
                <p>Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            </div>
        </div>
    </div>
</body>
</html>