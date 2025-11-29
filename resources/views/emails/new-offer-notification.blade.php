<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Offer Received - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">New Offer Received!</h2>

    <p>Dear {{ $recipient->name }},</p>

    <p>A new offer has been received for the property at <strong>{{ $property->address }}</strong>.</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Offer Details</h3>
        <p><strong>Offer Amount:</strong> £{{ number_format($offer->offer_amount, 2) }}</p>
        @if($property->asking_price)
            <p><strong>Asking Price:</strong> £{{ number_format($property->asking_price, 2) }}</p>
            <p><strong>Difference:</strong> 
                @php
                    $difference = $offer->offer_amount - $property->asking_price;
                    $percentage = $property->asking_price > 0 ? ($difference / $property->asking_price) * 100 : 0;
                @endphp
                @if($difference >= 0)
                    <span style="color: #28a745;">+£{{ number_format(abs($difference), 2) }} ({{ number_format(abs($percentage), 2) }}% above asking)</span>
                @else
                    <span style="color: #dc3545;">-£{{ number_format(abs($difference), 2) }} ({{ number_format(abs($percentage), 2) }}% below asking)</span>
                @endif
            </p>
        @endif
        <p><strong>Buyer:</strong> {{ $offer->buyer->name }}</p>
        <p><strong>Funding Type:</strong> {{ ucfirst(str_replace('_', ' ', $offer->funding_type ?? 'Not specified')) }}</p>
        @if($offer->deposit_amount)
            <p><strong>Deposit Amount:</strong> £{{ number_format($offer->deposit_amount, 2) }}</p>
        @endif
        @if($offer->conditions)
            <p><strong>Conditions:</strong> {{ $offer->conditions }}</p>
        @endif
        <p><strong>Offer Date:</strong> {{ $offer->created_at->format('l, F j, Y g:i A') }}</p>
    </div>

    <div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>Action Required:</strong> Please review this offer and respond through your dashboard.</p>
    </div>

    @if($recipient->role === 'seller' || $recipient->role === 'both')
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('seller.offer.decision.show', $offer->id) }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">Review Offer</a>
        </div>
    @else
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('admin.properties.show', $property->id) }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Property</a>
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
