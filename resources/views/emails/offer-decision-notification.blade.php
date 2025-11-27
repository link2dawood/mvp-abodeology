<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Decision - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    @if($decision === 'accepted')
        <h2 style="color: #28a745;">🎉 Congratulations! Your Offer Has Been Accepted!</h2>
        <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: 600; color: #155724;">Great news! The seller has accepted your offer of £{{ number_format($offer->offer_amount, 2) }} for the property at {{ $property->address }}.</p>
        </div>
    @elseif($decision === 'declined')
        <h2 style="color: #dc3545;">Offer Response - {{ $property->address }}</h2>
        <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #721c24;">We're sorry to inform you that your offer of £{{ number_format($offer->offer_amount, 2) }} has been declined by the seller.</p>
        </div>
    @elseif($decision === 'counter')
        <h2 style="color: #ffc107;">Counter-Offer Discussion Request</h2>
        <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #856404;">The seller has requested a counter-offer discussion regarding your offer of £{{ number_format($offer->offer_amount, 2) }}.</p>
        </div>
    @endif

    <p>Dear {{ $buyer->name }},</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Property Details</h3>
        <p><strong>Address:</strong> {{ $property->address }}</p>
        @if($property->postcode)
            <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
        @endif
        <p><strong>Your Offer:</strong> £{{ number_format($offer->offer_amount, 2) }}</p>
        <p><strong>Offer Date:</strong> {{ $offer->created_at->format('l, F j, Y') }}</p>
    </div>

    @if($decision === 'accepted')
        <div style="background: #E8F4F3; border-left: 4px solid #2CB8B4; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0; color: #2CB8B4;">What Happens Next?</h3>
            <ul style="margin: 10px 0; padding-left: 25px;">
                <li>A Memorandum of Sale has been automatically generated</li>
                <li>The Memorandum has been sent to both solicitors</li>
                <li>Sales progression workflow has begun</li>
                <li>You can track the progress through your dashboard</li>
            </ul>
            <p style="margin-top: 15px;"><strong>Important:</strong> Please ensure your solicitor has all the necessary information to proceed with the conveyancing process.</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('buyer.dashboard') }}" style="background: #28a745; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Your Dashboard</a>
        </div>
    @elseif($decision === 'declined')
        <p>We understand this may be disappointing. If you'd like to make another offer or view other properties, please feel free to browse our available listings.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('buyer.dashboard') }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Your Dashboard</a>
        </div>
    @elseif($decision === 'counter')
        <p>The seller would like to discuss a counter-offer. Our team will be in touch with you shortly to discuss the details.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('buyer.dashboard') }}" style="background: #ffc107; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Your Dashboard</a>
        </div>
    @endif

    <p>If you have any questions or need assistance, please don't hesitate to contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>

    <p>Best regards,<br>
    <strong>The Abodeology Team</strong></p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
