<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Valuation Request Submitted | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <style>
        /* BRAND COLOURS */
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
            --success-green: #28a745;
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
        .success-box {
            background: var(--white);
            width: 100%;
            max-width: 500px;
            padding: 40px;
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
            height: auto;
            object-fit: contain;
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }

        /* SUCCESS ICON */
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
            color: white;
        }

        /* HEADINGS */
        h2 {
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: 600;
            color: var(--success-green);
        }

        .message {
            font-size: 15px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* ALERT */
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
            color: #155724;
            font-size: 14px;
            text-align: left;
        }

        /* BUTTON */
        .btn {
            display: inline-block;
            background: var(--abodeology-teal);
            color: var(--white);
            padding: 14px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #25A29F;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            background: transparent;
            color: var(--abodeology-teal);
            border: 2px solid var(--abodeology-teal);
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: var(--abodeology-teal);
            color: var(--white);
        }

        /* FOOTER LINKS */
        .footer-links {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E5E5;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="success-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px;">Abodeology®</span>
            </div>
            
            <div class="success-icon">✓</div>
            
            <h2>Request Submitted Successfully!</h2>
            
            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="message">
                <p>Thank you for booking your property valuation with Abodeology.</p>
                <p>Our team will review your request and contact you shortly to confirm the details.</p>
                @if(session('success') && str_contains(session('success'), 'email'))
                    <p><strong>Please check your email</strong> for your login credentials. You can log in to track the status of your valuation request.</p>
                @endif
            </div>

            <div style="margin-top: 30px;">
                <a href="{{ route('login') }}" class="btn">Log In to Your Account</a>
                @if(session('success') && !str_contains(session('success'), 'email'))
                    <a href="{{ route('register') }}" class="btn btn-secondary">Create Account</a>
                @endif
            </div>

            <div class="footer-links">
                <p>Need help? <a href="mailto:support@abodeology.co.uk">Contact Support</a></p>
            </div>
        </div>
    </div>
</body>
</html>

