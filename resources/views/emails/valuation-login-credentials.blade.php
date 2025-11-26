<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Abodeology</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            background: #0F0F0F;
            padding: 20px;
            border-radius: 8px;
        }
        .logo-text {
            color: #2CB8B4;
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 1px;
        }
        h1 {
            color: #2CB8B4;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            color: #1E1E1E;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        p {
            margin-bottom: 15px;
            font-size: 15px;
        }
        .credentials-box {
            background-color: #F4F4F4;
            border: 2px solid #2CB8B4;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .credentials-box strong {
            color: #1E1E1E;
            display: inline-block;
            width: 120px;
            font-size: 14px;
        }
        .credentials-box .value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #2CB8B4;
        }
        .button {
            display: inline-block;
            background-color: #2CB8B4;
            color: #ffffff;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 25px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #25A29F;
        }
        .info-box {
            background-color: #E8F4F3;
            border-left: 4px solid #2CB8B4;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E5E5;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #2CB8B4;
            text-decoration: none;
        }
        ul {
            margin: 10px 0;
            padding-left: 25px;
        }
        li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">
            <h1 class="logo-text">Abodeology®</h1>
        </div>

        <h1>Welcome to Abodeology!</h1>

        <p>Hello {{ $user->name }},</p>

        <p>Thank you for booking a property valuation with Abodeology. Your account has been automatically created for you.</p>

        @if(isset($valuation))
        <div class="info-box">
            <p><strong>Valuation Request Details:</strong></p>
            <p>Property Address: {{ $valuation->property_address }}</p>
            @if($valuation->postcode)
                <p>Postcode: {{ $valuation->postcode }}</p>
            @endif
            @if($valuation->valuation_date)
                <p>Preferred Date: {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}</p>
            @endif
        </div>
        @endif

        <h2>Your Login Credentials</h2>

        <div class="credentials-box">
            <p style="margin: 0 0 10px 0;"><strong>Email:</strong> <span class="value">{{ $user->email }}</span></p>
            <p style="margin: 0;"><strong>Password:</strong> <span class="value">{{ $password }}</span></p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Log In to Your Account</a>
        </div>

        <div class="info-box">
            <p><strong>⚠️ Security Note:</strong></p>
            <p>Please change your password after logging in for the first time. You can do this from your account profile settings.</p>
        </div>

        <h2>What's Next?</h2>
        <ul>
            <li>Our team will review your valuation request</li>
            <li>We'll contact you to confirm the valuation appointment details</li>
            <li>You can log in to track the status of your valuation request</li>
            @if($user->role === 'seller' || $user->role === 'both')
                <li>Once the valuation is complete, you can access your seller dashboard to manage your property</li>
            @elseif($user->role === 'buyer')
                <li>Once the valuation is complete, you can access your buyer dashboard to view properties</li>
            @endif
        </ul>

        <h2>Need Help?</h2>
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team:</p>
        <p>
            Email: <a href="mailto:support@abodeology.co.uk">support@abodeology.co.uk</a><br>
            We're here to help!
        </p>

        <div class="footer">
            <p>This email was sent to {{ $user->email }} because you booked a property valuation on Abodeology.</p>
            <p>
                <a href="{{ route('login') }}">Log In</a> | 
                <a href="mailto:support@abodeology.co.uk">Contact Support</a>
            </p>
            <p>&copy; {{ date('Y') }} Abodeology. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

