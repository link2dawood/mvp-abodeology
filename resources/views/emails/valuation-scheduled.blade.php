<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valuation Scheduled - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Your Valuation Appointment is Scheduled</h2>

    <p>Dear {{ $seller->name ?? 'Customer' }},</p>

    <p>Thanks for booking your property valuation with Abodeology. Your valuation appointment has now been scheduled.</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Appointment Details</h3>
        <p><strong>Property Address:</strong> {{ $valuation->property_address }}</p>
        @if($valuation->postcode)
            <p><strong>Postcode:</strong> {{ $valuation->postcode }}</p>
        @endif
        @if($valuation->valuation_date)
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}</p>
        @endif
        @if($valuation->valuation_time)
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</p>
        @endif
        @if($agent)
            <p><strong>Agent/Valuer:</strong> {{ $agent->name }} ({{ $agent->email }})</p>
        @endif
    </div>

    <p>If you need to make any changes to your appointment, just reply to this email and our team will help.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Log In to Your Account</a>
    </div>

    <p>If you have any questions, please contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>

    <p>Kind regards,<br>
    The Abodeology Team</p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>

