<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Valuation Request</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 180px;
            height: auto;
        }
        h1 {
            color: #2CB8B4;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            color: #1E1E1E;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #2CB8B4;
            padding-bottom: 8px;
        }
        p {
            margin-bottom: 15px;
            font-size: 15px;
        }
        .info-box {
            background-color: #E8F4F3;
            border-left: 4px solid #2CB8B4;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .info-box strong {
            color: #1E1E1E;
            display: inline-block;
            min-width: 140px;
        }
        .button {
            display: inline-block;
            background-color: #2CB8B4;
            color: #ffffff;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 25px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #25A29F;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #FFF3CD;
            color: #856404;
        }
        .status-scheduled {
            background-color: #D1ECF1;
            color: #0C5460;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E5E5;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #2CB8B4;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'">
        </div>

        <h1>New Valuation Appointment Request</h1>

        <p>Hello,</p>

        <p>A new property valuation request has been submitted and requires your attention.</p>

        <h2>Request Details</h2>

        <div class="info-box">
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $valuation->status }}">
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

        <h2>Client Information</h2>

        <div class="info-box">
            <p><strong>Name:</strong> {{ $seller->name }}</p>
            <p><strong>Email:</strong> {{ $seller->email }}</p>
            @if($seller->phone)
                <p><strong>Phone:</strong> {{ $seller->phone }}</p>
            @endif
            <p><strong>Role:</strong> {{ ucfirst($seller->role) }}</p>
        </div>

        @if($valuation->seller_notes)
        <h2>Additional Notes</h2>
        <div class="info-box">
            <p>{{ $valuation->seller_notes }}</p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ $dashboardUrl }}" class="button">View in Dashboard</a>
        </div>

        <div class="footer">
            <p>This is an automated notification from Abodeology.</p>
            <p>
                <a href="{{ $dashboardUrl }}">Admin Dashboard</a> | 
                <a href="mailto:support@abodeology.com">Contact Support</a>
            </p>
            <p>&copy; {{ date('Y') }} Abodeology. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

