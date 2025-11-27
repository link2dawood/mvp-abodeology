@extends('layouts.buyer')

@section('title', 'Buyer Dashboard')

@push('styles')
<style>
    /* PAGE WRAPPER */
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        margin-bottom: 8px;
        font-size: 28px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 28px;
    }

    /* GRID */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 25px;
    }

    /* CARD */
    .card {
        background: var(--white);
        border-radius: 12px;
        padding: 25px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: 600;
    }

    .card p {
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.6;
    }

    .card p strong {
        font-weight: 600;
    }

    /* STATUS BADGES */
    .status {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-verified { 
        background: var(--abodeology-teal); 
        color: var(--white); 
    }

    .status-pending { 
        background: #DDD; 
        color: #333; 
    }

    .status-failed { 
        background: #E14F4F; 
        color: #FFF; 
    }

    /* TABLE */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    .table td {
        padding: 10px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    /* BUTTON */
    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px 16px;
        display: inline-block;
        border-radius: 6px;
        margin-top: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.3s ease;
        font-size: 14px;
    }

    .btn:hover {
        background: #25A29F;
    }

    /* LIST */
    .card ul {
        padding-left: 18px;
        margin: 0 0 15px 0;
        list-style: none;
    }

    .card ul li {
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 1.6;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        h2 {
            font-size: 24px;
        }

        .grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .card {
            padding: 20px;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
            white-space: nowrap;
        }

        .btn {
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 20px;
        }

        .card {
            padding: 15px;
        }

        .table th,
        .table td {
            padding: 6px;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Welcome back, {{ $buyerName ?? auth()->user()->name }}</h2>
    <div class="page-subtitle">Your activity, offers, viewings and verification all in one place.</div>

    <div class="grid">
        <!-- VERIFICATION STATUS -->
        <div class="card">
            <h3>Your Verification</h3>
            <p>Status:
                <span class="status status-{{ $amlStatusClass ?? 'pending' }}">
                    {{ $amlStatusText ?? 'Pending' }}
                </span>
            </p>
            <p>To submit offers without delay, please complete your AML checks.</p>
            <a href="{{ route('buyer.aml.upload') }}" class="btn">Complete Verification</a>
        </div>

        <!-- UPCOMING VIEWINGS -->
        <div class="card">
            <h3>Upcoming Viewings</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Date</th>
                </tr>
                @forelse($upcomingViewings ?? [] as $viewing)
                    <tr>
                        <td>{{ $viewing['property'] ?? 'N/A' }}</td>
                        <td>{{ $viewing['date'] ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No upcoming viewings</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn">Book a Viewing</a>
        </div>

        <!-- OFFERS -->
        <div class="card">
            <h3>Your Offers</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Offer (£)</th>
                    <th>Status</th>
                </tr>
                @forelse($offers ?? [] as $offer)
                    <tr>
                        <td>{{ $offer['property'] ?? 'N/A' }}</td>
                        <td>£{{ number_format($offer['amount'] ?? 0, 2) }}</td>
                        <td>
                            <span class="status status-{{ strtolower($offer['status'] ?? 'pending') }}">
                                {{ $offer['status'] ?? 'Pending' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No offers submitted</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn">Make an Offer</a>
        </div>

        <!-- NOTIFICATIONS -->
        <div class="card">
            <h3>Notifications</h3>
            <ul>
                @forelse($notifications ?? [] as $notification)
                    <li>{{ $notification }}</li>
                @empty
                    <li style="color: #999;">No new notifications</li>
                @endforelse
            </ul>
            <a href="#" class="btn">View All Notifications</a>
        </div>

        <!-- SOLICITOR DETAILS -->
        <div class="card">
            <h3>Your Solicitor</h3>
            <p><strong>Name:</strong> {{ $solicitorName ?? 'Not set' }}</p>
            <p><strong>Firm:</strong> {{ $solicitorFirm ?? 'Not set' }}</p>
            <p><strong>Email:</strong> {{ $solicitorEmail ?? 'Not set' }}</p>
            <p><strong>Phone:</strong> {{ $solicitorPhone ?? 'Not set' }}</p>
            <a href="#" class="btn">Update Solicitor</a>
        </div>
    </div>
</div>
@endsection
