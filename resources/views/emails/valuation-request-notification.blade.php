<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Valuation Request - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">New Valuation Appointment Request</h2>

    <p>Hello,</p>

    <p>A new property valuation request has been submitted and requires your attention.</p>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Request Details</h3>
        <p><strong>Status:</strong> 
            <span style="display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: uppercase; background-color: {{ $valuation->status === 'pending' ? '#FFF3CD' : '#D1ECF1' }}; color: {{ $valuation->status === 'pending' ? '#856404' : '#0C5460' }};">
                {{ ucfirst($valuation->status) }}
            </span>
        </p>
        <p><strong>Property Address:</strong> {{ $valuation->property_address }}</p>
        @if($valuation->postcode)
            <p><strong>Postcode:</strong> {{ $valuation->postcode }}</p>
        @endif
        @if($valuation->property_type)
            <p><strong>Property Type:</strong> {{ ucfirst(str_replace('_', ' ', $valuation->property_type)) }}</p>
        @endif
        @if($valuation->bedrooms)
            <p><strong>Bedrooms:</strong> {{ $valuation->bedrooms }}</p>
        @endif
        @if($valuation->valuation_date)
            <p><strong>Preferred Date:</strong> {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}</p>
        @endif
        @if($valuation->valuation_time)
            <p><strong>Preferred Time:</strong> {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</p>
        @endif
    </div>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Client Information</h3>
        <p><strong>Name:</strong> {{ $seller->name }}</p>
        <p><strong>Email:</strong> {{ $seller->email }}</p>
        @if($seller->phone)
            <p><strong>Phone:</strong> {{ $seller->phone }}</p>
        @endif
        <p><strong>Role:</strong> {{ ucfirst($seller->role) }}</p>
    </div>

    @if($valuation->seller_notes)
    <div style="background: #E8F4F3; border-left: 4px solid #2CB8B4; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Additional Notes</h3>
        <p style="margin: 0;">{{ $valuation->seller_notes }}</p>
    </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $dashboardUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View in Dashboard</a>
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
