<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard | Abodeology</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
    :root {
        --primary: #32b3ac;
        --primary-light: #49c5bd;
        --text: #222;
        --muted: #666;
        --bg: #f7f7f7;
        --white: #fff;
        --black: #000;
    }

    body {
        margin: 0;
        background: var(--bg);
        font-family: "Inter", Arial, sans-serif;
        color: var(--text);
    }

    /* HEADER */
    .header {
        background: var(--black);
        padding: 18px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo img {
        width: 190px;
        height: auto;
        object-fit: contain;
    }

    .nav a {
        color: #fff;
        margin-left: 24px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
    }

    .nav a:hover {
        color: var(--primary);
    }

    /* MAIN CONTAINER */
    .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h2.section-title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--text);
    }

    /* CARD */
    .card {
        background: var(--white);
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    }

    /* UPCOMING VALUATION BOX */
    .valuation-box {
        background: #fafafa;
        padding: 20px 22px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .valuation-box strong {
        font-size: 18px;
        color: var(--text);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        background: var(--primary);
        color: white;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 10px;
    }

    /* CHECKLIST */
    .checklist-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--text);
    }

    .checklist-item {
        margin: 8px 0;
        font-size: 15px;
        line-height: 1.6;
    }

    .checklist-item::before {
        content: "✔ ";
        color: var(--primary);
        font-weight: 700;
        margin-right: 8px;
    }

    .awaiting-box {
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        padding: 18px;
        color: #c0392b;
        background: #fff5f5;
        border-radius: 8px;
    }

    footer {
        text-align: center;
        font-size: 14px;
        color: var(--muted);
        padding: 40px 0;
        margin-top: 50px;
    }

    footer a {
        color: var(--primary);
        text-decoration: none;
        margin: 0 10px;
    }

    footer a:hover {
        text-decoration: underline;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .header {
            padding: 15px 20px;
            flex-wrap: wrap;
        }

        .logo img {
            width: 150px;
        }

        .nav {
            width: 100%;
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
        }

        .nav a {
            margin: 8px 12px 8px 0;
        }

        .container {
            padding: 0 15px;
            margin: 20px auto;
        }

        h2.section-title {
            font-size: 22px;
        }

        .card {
            padding: 20px;
        }
    }
    </style>
</head>
<body>
<!-- HEADER -->
<div class="header">
    <div class="logo">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <span style="display: none; color: #fff; font-weight: 600; font-size: 20px;">Abodeology®</span>
    </div>
    <div class="nav">
        <a href="{{ route('seller.dashboard') }}">Dashboard</a>
        <a href="{{ route('profile.show') }}">Profile</a>
        <a href="#">Notifications</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
        </form>
    </div>
</div>

<div class="container">
    <!-- UPCOMING VALUATION -->
    <h2 class="section-title">Upcoming Valuations</h2>
    @if(isset($upcomingValuations) && $upcomingValuations->count() > 0)
        @foreach($upcomingValuations as $valuation)
            <div class="card">
                <div class="valuation-box">
                    <strong>{{ $valuation->property_address ?? 'N/A' }}</strong><br>
                    @if($valuation->postcode)
                        {{ $valuation->postcode }}<br>
                    @endif
                    <br>
                    @if($valuation->valuation_date && $valuation->valuation_time)
                        <strong>Valuation Date:</strong> 
                        {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }} @ 
                        {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}
                    @elseif($valuation->valuation_date)
                        <strong>Valuation Date:</strong> 
                        {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}
                    @else
                        <strong>Status:</strong> Pending scheduling
                    @endif
                    <br>
                    <span class="status-badge">Status: {{ ucfirst($valuation->status ?? 'Pending') }}</span>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="valuation-box" style="text-align: center; color: var(--muted);">
                No upcoming valuations scheduled.
            </div>
        </div>
    @endif

    <!-- AWAITING VALUATION & HOMECHECK -->
    <h2 class="section-title">Your Valuation & HomeCheck Results</h2>
    <div class="card awaiting-box">
        Awaiting Valuation & HomeCheck.
    </div>

    <!-- CHECKLIST -->
    <div class="card">
        <div class="checklist-title">
            Valuation & HomeCheck Preparation Checklist
        </div>
        <div class="checklist-item">Tidy key rooms and clear surfaces (kitchen, living room, bathrooms).</div>
        <div class="checklist-item">Open blinds/curtains and switch on lights for brighter 360 photos.</div>
        <div class="checklist-item">Ensure all rooms, cupboards and areas are accessible (no locked spaces).</div>
        <div class="checklist-item">Ensure boiler, fuse board and meter cupboards can be easily opened.</div>
        <div class="checklist-item">Clear floors and walkways to allow smooth 360 image capture.</div>
        <div class="checklist-item">Make sure pets are managed or out of the way during the appointment.</div>
        <div class="checklist-item">Move bins, laundry and personal clutter out of main areas.</div>
        <div class="checklist-item">Have any documents handy (boiler service, certificates, extension paperwork).</div>
        <div class="checklist-item">Prepare any questions you'd like to ask about price, condition or improvements.</div>
    </div>
</div>

<footer>
    © {{ date('Y') }} Abodeology®. All rights reserved.<br>
    <a href="#">Privacy Policy</a> |
    <a href="#">Terms of Service</a> |
    <a href="mailto:support@abodeology.co.uk">Contact Support</a>
</footer>
</body>
</html>

