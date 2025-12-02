<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Required - Memorandum of Sale - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Action Required: Complete Your Information</h2>

    @if($role === 'seller')
        <p>Dear {{ $property->seller->name }},</p>
        <p>Congratulations! Your offer for <strong>{{ $property->address }}</strong> has been accepted.</p>
        <p>However, before we can generate the Memorandum of Sale, we need you to complete your solicitor details in your dashboard.</p>
        <div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0;"><strong>Required:</strong> Please provide your solicitor's contact information through your dashboard.</p>
        </div>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('seller.solicitor.details', $property->id) }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Complete Solicitor Details</a>
        </div>
    @else
        <p>Dear {{ $offer->buyer->name }},</p>
        <p>Great news! Your offer for <strong>{{ $property->address }}</strong> has been accepted.</p>
        <p>However, before we can generate the Memorandum of Sale, we need you to complete your solicitor details in your profile.</p>
        <div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0;"><strong>Required:</strong> Please provide your solicitor's contact information through your profile.</p>
        </div>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('buyer.profile') }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Complete Solicitor Details</a>
        </div>
    @endif

    <p>Once both parties have completed their information, the Memorandum of Sale will be automatically generated and sent to all required parties.</p>

    <p>If you have any questions or need assistance, please don't hesitate to contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>

    <p>Best regards,<br>
    <strong>The Abodeology Team</strong></p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>

