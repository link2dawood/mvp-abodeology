<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Viewing Request - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2CB8B4; margin: 0;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">New Viewing Request</h2>

    <p>Dear Viewing Partner,</p>

    <p>A new viewing request has been submitted for a property. Please review the details below and contact the buyer to confirm the appointment.</p>

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
        <h3 style="margin-top: 0; color: #2CB8B4;">Viewing Details</h3>
        <p><strong>Requested Date:</strong> {{ \Carbon\Carbon::parse($viewing->viewing_date)->format('l, F j, Y') }}</p>
        <p><strong>Requested Time:</strong> {{ \Carbon\Carbon::parse($viewing->viewing_date)->format('g:i A') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($viewing->status) }}</p>
    </div>

    <div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2CB8B4;">Buyer Details</h3>
        <p><strong>Name:</strong> {{ $buyer->name }}</p>
        <p><strong>Email:</strong> {{ $buyer->email }}</p>
        @if($buyer->phone)
            <p><strong>Phone:</strong> {{ $buyer->phone }}</p>
        @endif
    </div>

    <div style="background: #E8F4F3; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <p style="margin: 0;"><strong>Action Required:</strong> Please contact the buyer to confirm the viewing appointment and complete the viewing feedback form after the viewing.</p>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $viewingUrl }}" style="background: #2CB8B4; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: 600;">
            View Full Details
        </a>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ $dashboardUrl }}" style="color: #2CB8B4; text-decoration: underline;">
            Go to Viewings Dashboard
        </a>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666;">
        <p>This is an automated notification from the Abodeology platform.</p>
        <p>If you have any questions, please contact the admin team.</p>
    </div>
</body>
</html>

