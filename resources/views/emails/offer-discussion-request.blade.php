<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Discussion Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #32b3ac; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Offer Discussion Request</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px;">
        <p><strong>{{ $seller->name }}</strong> has requested to discuss an offer with you.</p>
        
        <div style="background: white; padding: 15px; border-radius: 6px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #32b3ac;">Offer Details</h3>
            <p><strong>Property:</strong> {{ $property->address }}</p>
            <p><strong>Buyer:</strong> {{ $offer->buyer->name ?? 'N/A' }}</p>
            <p><strong>Offer Amount:</strong> £{{ number_format($offer->offer_amount, 0) }}</p>
            <p><strong>Offer Status:</strong> {{ ucfirst($offer->status) }}</p>
        </div>

        <div style="background: white; padding: 15px; border-radius: 6px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #32b3ac;">Discussion Request Details</h3>
            <p><strong>Preferred Contact Method:</strong> {{ ucfirst($requestData['contact_method'] ?? 'Phone Call') }}</p>
            <p><strong>Urgency:</strong> 
                @if(($requestData['urgency'] ?? 'normal') === 'urgent')
                    <span style="color: #e67c22; font-weight: 600;">URGENT - Today if possible</span>
                @elseif(($requestData['urgency'] ?? 'normal') === 'asap')
                    <span style="color: #dc3545; font-weight: 600;">ASAP - Immediate callback requested</span>
                @else
                    Normal - Within 24 hours
                @endif
            </p>
            @if(!empty($requestData['discussion_points']))
                <p><strong>Discussion Points:</strong></p>
                <p style="background: #f0f0f0; padding: 10px; border-radius: 4px; font-style: italic;">{{ $requestData['discussion_points'] }}</p>
            @endif
        </div>

        <div style="background: #fff6d6; padding: 15px; border-radius: 6px; margin: 15px 0;">
            <p style="margin: 0; color: #7a6300;"><strong>Seller Contact Information:</strong></p>
            <p style="margin: 5px 0; color: #7a6300;">Name: {{ $seller->name }}</p>
            <p style="margin: 5px 0; color: #7a6300;">Email: {{ $seller->email }}</p>
            @if($seller->phone)
                <p style="margin: 5px 0; color: #7a6300;">Phone: {{ $seller->phone }}</p>
            @endif
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('admin.properties.show', $property->id) }}" style="background: #32b3ac; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Property & Offer Details</a>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>This is an automated notification from Abodeology.</p>
    </div>
</body>
</html>

