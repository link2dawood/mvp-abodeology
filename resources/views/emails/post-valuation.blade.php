<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Valuation Follow-Up - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2CB8B4; margin: 0;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Post-Valuation Follow-Up</h2>

    <p>Dear {{ $seller->name }},</p>

    <p>Thank you for choosing Abodeology for your property valuation. We hope you found the valuation appointment helpful and informative.</p>

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

    <p><strong>Ready to proceed?</strong></p>
    <p>If you're ready to move forward with listing your property, you can now instruct Abodeology to act as your estate agent. By clicking the button below, you'll be able to:</p>
    <ul>
        <li>Review and sign the Estate Agency Terms & Conditions</li>
        <li>Complete your instruction to sell</li>
        <li>Get started with marketing your property</li>
    </ul>

    <p>Once you sign the Terms & Conditions, we'll send you a Welcome Pack with all the information you need to get started.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $instructUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 15px 40px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; font-size: 16px;">Instruct Abodeology</a>
    </div>

    <p>If you have any questions or would like to discuss your options further, please don't hesitate to contact us. We're here to help you every step of the way.</p>

    <p>Best regards,<br>
    The Abodeology Team</p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
