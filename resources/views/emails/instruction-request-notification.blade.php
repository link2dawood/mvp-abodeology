<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Terms & Conditions - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2CB8B4; margin: 0;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Action Required: Sign Your Terms & Conditions</h2>

    <p>Dear {{ $seller->name }},</p>

    <p>Your agent has requested that you sign the Terms & Conditions to proceed with listing your property for sale.</p>

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

    <p><strong>Next Steps:</strong></p>
    <ul>
        <li>Review the Estate Agency Terms & Conditions</li>
        <li>Complete the required declarations</li>
        <li>Provide your digital signature</li>
        <li>Submit the instruction form</li>
    </ul>

    <p>Once you sign the Terms & Conditions, your property will be one step closer to going live on the market.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $instructUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Sign Terms & Conditions Now</a>
    </div>

    <p>If you have any questions or would prefer to sign later, please contact your agent.</p>

    <p>Best regards,<br>
    The Abodeology Team</p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
