<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Pack - Abodeology</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2CB8B4; margin: 0;">Abodeology®</h1>
    </div>

    <h2 style="color: #2CB8B4;">Welcome Pack - Instruction Signed Successfully!</h2>

    <p>Dear {{ $user->name }},</p>

    <p>Congratulations! Your instruction to sell your property has been signed successfully. We're excited to work with you to sell your property.</p>

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
        <h3 style="margin-top: 0; color: #2CB8B4;">Instruction Details</h3>
        <p><strong>Signed Date:</strong> {{ \Carbon\Carbon::parse($instruction->signed_at)->format('l, F j, Y') }}</p>
        <p><strong>Fee Percentage:</strong> {{ number_format($instruction->fee_percentage, 2) }}%</p>
        <p><strong>Status:</strong> Signed</p>
    </div>

    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #856404;">⚠️ Action Required: Upload AML Documents</h3>
        <p style="color: #856404; margin: 10px 0;">To proceed with your property sale, please upload your AML documents:</p>
        <ul style="color: #856404; margin: 10px 0; padding-left: 20px;">
            <li><strong>Photo ID:</strong> Passport, Driving License, or National ID</li>
            <li><strong>Proof of Address:</strong> Utility bill, Bank statement, or Council tax bill (dated within last 3 months)</li>
        </ul>
        <p style="color: #856404; margin: 10px 0; font-size: 14px;">
            <em>Note: Your agent has already visually checked your ID at the valuation appointment as required by HMRC/EA Act. Please upload these documents via your dashboard to complete the AML verification process.</em>
        </p>
    </div>

    <h3 style="color: #2CB8B4;">What's Next?</h3>
    <ol>
        <li><strong>Upload AML Documents:</strong> Please upload your ID and Proof of Address via your dashboard</li>
        <li><strong>Provide Solicitor Details:</strong> Share your solicitor's contact information</li>
        <li><strong>HomeCheck:</strong> Our team will schedule and complete an Abodeology HomeCheck</li>
        <li><strong>Property Listing:</strong> We'll prepare marketing materials and list your property</li>
        <li><strong>Track Progress:</strong> Monitor everything through your seller dashboard</li>
    </ol>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $sellerDashboardUrl }}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">View Your Dashboard & Upload AML Documents</a>
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
