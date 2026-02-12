<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Welcome to Abodeology!</h2>

    <p>Hello {{ $user->name }},</p>

    <p>Thanks for booking your property valuation with Abodeology. We've received your request and created your account so you can manage everything in one place.</p>

    @if(isset($valuation))
    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Valuation Request Details</h3>
        <p><strong>Property Address:</strong> {{ $valuation->property_address }}</p>
    </div>
    @endif

    <h3 style="color: #2CB8B4;">Your Login Details</h3>

    <div style="background: #F4F4F4; border: 2px solid #2CB8B4; border-radius: 8px; padding: 20px; margin: 25px 0;">
        <p style="margin: 0 0 10px 0;"><strong>Email:</strong> <span style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{ $user->email }}</span></p>
        <p style="margin: 0;"><strong>Temporary password:</strong> <span style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{ $password }}</span></p>
    </div>

    <p>You can log in at any time to view your request and any updates. For security, we recommend changing your password after your first login.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Log In to Your Account</a>
    </div>

    <p>If you have any questions before your valuation, just reply to this email — we're here to help.</p>

    <p>Kind regards,<br>
    The Abodeology Team</p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
