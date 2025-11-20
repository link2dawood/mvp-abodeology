<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Pack - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2CB8B4; margin: 0;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Welcome Pack - Instruction Signed Successfully!</h2>

    <p>Dear {{ $user->name }},</p>

    <p>Congratulations! Your instruction to sell your property has been signed successfully. We're excited to work with you to sell your property.</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Property Details</h3>
        <p><strong>Address:</strong> {{ $property->address }}</p>
        @if($property->postcode)
            <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
        @endif
        @if($property->asking_price)
            <p><strong>Asking Price:</strong> £{{ number_format($property->asking_price, 0) }}</p>
        @endif
    </div>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Instruction Details</h3>
        <p><strong>Signed Date:</strong> {{ \Carbon\Carbon::parse($instruction->signed_at)->format('l, F j, Y') }}</p>
        <p><strong>Fee Percentage:</strong> {{ number_format($instruction->fee_percentage, 2) }}%</p>
        <p><strong>Status:</strong> Signed</p>
    </div>

    <h3 style="color: #2CB8B4;">What's Next?</h3>
    <ul>
        <li>Our team will review your property listing</li>
        <li>We'll prepare marketing materials for your property</li>
        <li>Your property will be prepared for listing on the market</li>
        <li>You can track the progress through your seller dashboard</li>
    </ul>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $sellerDashboardUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Your Dashboard</a>
    </div>

    <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>

    <p>Best regards,<br>
    The Abodeology Team</p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
