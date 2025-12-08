<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Viewing Assigned - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">New Viewing Assigned</h2>

    <p>Hello {{ $pva->name }},</p>

    <p>A new viewing has been assigned to you. Please review the details below and confirm your availability.</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Viewing Details</h3>
        <p><strong>Property Address:</strong> {{ $property->address }}</p>
        @if($property->postcode)
            <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
        @endif
        @if($viewing->viewing_date)
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($viewing->viewing_date)->format('l, F j, Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($viewing->viewing_date)->format('g:i A') }}</p>
        @endif
        <p><strong>Buyer:</strong> {{ $viewing->buyer->name ?? 'N/A' }}</p>
        @if($viewing->buyer->phone)
            <p><strong>Buyer Phone:</strong> {{ $viewing->buyer->phone }}</p>
        @endif
        @if($viewing->special_instructions)
            <p><strong>Special Instructions:</strong> {{ $viewing->special_instructions }}</p>
        @endif
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('pva.viewings.show', $viewing->id) }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Full Details</a>
    </div>

    <div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>Next Steps:</strong></p>
        <ul style="margin: 10px 0; padding-left: 25px;">
            <li>Review the viewing details in your PVA dashboard</li>
            <li>Confirm the viewing if you're available</li>
            <li>Contact the buyer if you need to coordinate any details</li>
            <li>Submit feedback after completing the viewing</li>
        </ul>
    </div>

    <p>If you have any questions or concerns about this assignment, please contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>

    <p>Best regards,<br>
    <strong>The Abodeology Team</strong></p>

    <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
    <p style="font-size: 12px; color: #666; text-align: center;">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </p>
</body>
</html>
