<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Status Changed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #2CB8B4; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Abodeology</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px;">
        <h2 style="color: #2CB8B4; margin-top: 0;">
            @if($status === 'sold')
                Property Has Been Sold
            @elseif($status === 'withdrawn')
                Property Has Been Withdrawn
            @elseif($status === 'sstc')
                Property Status Updated
            @else
                Property Status Changed
            @endif
        </h2>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>{{ $message }}</p>
        
        <div style="background: white; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #2CB8B4;">
            <p style="margin: 0 0 10px 0;"><strong>Property Address:</strong></p>
            <p style="margin: 0; font-size: 18px; color: #1E1E1E;">{{ $property->address }}</p>
            @if($property->postcode)
                <p style="margin: 5px 0 0 0; color: #666;">{{ $property->postcode }}</p>
            @endif
        </div>
        
        @if($status === 'sold')
            <p style="color: #856404; background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                <strong>Note:</strong> This property has been sold and is no longer available. Any pending offers or scheduled viewings have been automatically cancelled.
            </p>
        @elseif($status === 'withdrawn')
            <p style="color: #856404; background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                <strong>Note:</strong> This property has been withdrawn from the market. Any pending offers or scheduled viewings have been automatically cancelled.
            </p>
        @endif
        
        <p style="margin-top: 30px;">
            If you have any questions, please don't hesitate to contact us.
        </p>
        
        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>The Abodeology Team</strong>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>This is an automated notification from Abodeology.</p>
    </div>
</body>
</html>

