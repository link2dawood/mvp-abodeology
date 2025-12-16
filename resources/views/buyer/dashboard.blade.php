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

    <!-- METRICS SECTION -->
    @if(isset($metrics))
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
                <div style="font-size: 32px; font-weight: 700; color: var(--abodeology-teal); margin-bottom: 5px;">
                    {{ $metrics['total_offers'] ?? 0 }}
                </div>
                <div style="font-size: 14px; color: #666;">Total Offers</div>
            </div>
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #fff3cd 0%, #F4F4F4 100%);">
                <div style="font-size: 32px; font-weight: 700; color: #ffc107; margin-bottom: 5px;">
                    {{ $metrics['pending_offers'] ?? 0 }}
                </div>
                <div style="font-size: 14px; color: #666;">Pending Offers</div>
            </div>
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #d4edda 0%, #F4F4F4 100%);">
                <div style="font-size: 32px; font-weight: 700; color: #28a745; margin-bottom: 5px;">
                    {{ $metrics['accepted_offers'] ?? 0 }}
                </div>
                <div style="font-size: 14px; color: #666;">Accepted Offers</div>
            </div>
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
                <div style="font-size: 32px; font-weight: 700; color: var(--abodeology-teal); margin-bottom: 5px;">
                    {{ $metrics['upcoming_viewings'] ?? 0 }}
                </div>
                <div style="font-size: 14px; color: #666;">Upcoming Viewings</div>
            </div>
        </div>
    @endif

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
            @if(isset($upcomingViewings) && $upcomingViewings->count() > 0)
                <table class="table">
                    <tr>
                        <th>Property</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @foreach($upcomingViewings as $viewing)
                        <tr>
                            <td>
                                <strong>{{ $viewing->property->address ?? 'N/A' }}</strong>
                                @if($viewing->property->asking_price)
                                    <br><span style="font-size: 12px; color: #666;">£{{ number_format($viewing->property->asking_price, 0) }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y') : 'N/A' }}
                                <br><span style="font-size: 12px; color: #666;">{{ $viewing->viewing_date ? $viewing->viewing_date->format('g:i A') : '' }}</span>
                            </td>
                            <td>
                                <span class="status status-{{ strtolower($viewing->status ?? 'scheduled') }}">
                                    {{ ucfirst($viewing->status ?? 'Scheduled') }}
                                </span>
                            </td>
                            <td>
                                @if($viewing->property)
                                    <a href="{{ route('buyer.viewing.request', $viewing->property->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No upcoming viewings</p>
                <a href="#" class="btn">Book a Viewing</a>
            @endif
        </div>

        <!-- BOOK VALUATION -->
        <div class="card">
            <h3>Get Your Property Valued</h3>
            <p style="margin: 10px 0; font-size: 14px; line-height: 1.6;">
                Thinking of selling? Book a free property valuation to find out how much your property is worth.
            </p>
            <a href="{{ route('valuation.booking') }}" class="btn">Book a Valuation</a>
        </div>

        <!-- OFFERS -->
        <div class="card">
            <h3>Your Offers</h3>
            @if(isset($offers) && count($offers) > 0)
                <table class="table">
                    <tr>
                        <th>Property</th>
                        <th>Offer (£)</th>
                        <th>Status</th>
                        <th>Outcome</th>
                        <th>Date</th>
                    </tr>
                    @foreach($offers as $offer)
                        <tr>
                            <td>
                                <strong>{{ $offer['property'] ?? 'N/A' }}</strong>
                                @if(isset($offer['property_id']))
                                    <br><a href="{{ route('buyer.viewing.request', $offer['property_id']) }}" style="font-size: 12px; color: var(--abodeology-teal);">View Property</a>
                                @endif
                            </td>
                            <td>£{{ number_format($offer['amount'] ?? 0, 2) }}</td>
                            <td>
                                @if($offer['status'] === 'pending')
                                    <span class="status status-pending">Pending</span>
                                @elseif($offer['status'] === 'accepted')
                                    <span class="status" style="background: #28a745; color: #fff;">Accepted</span>
                                @elseif($offer['status'] === 'declined')
                                    <span class="status" style="background: #dc3545; color: #fff;">Declined</span>
                                @elseif($offer['status'] === 'countered')
                                    <span class="status" style="background: #17a2b8; color: #fff;">Countered</span>
                                @else
                                    <span class="status status-pending">{{ ucfirst($offer['status'] ?? 'Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($offer['outcome'])
                                    @if($offer['decision'] === 'accepted')
                                        <strong style="color: #28a745;">✓ Accepted</strong>
                                    @elseif($offer['decision'] === 'declined')
                                        <strong style="color: #dc3545;">✗ Declined</strong>
                                    @elseif($offer['decision'] === 'counter')
                                        <strong style="color: #17a2b8;">Counter: £{{ number_format($offer['counter_amount'] ?? 0, 2) }}</strong>
                                    @endif
                                @else
                                    <span style="color: #999;">Awaiting response</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #666;">
                                @if(isset($offer['created_at']))
                                    {{ $offer['created_at']->format('M j, Y') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No offers submitted yet</p>
            @endif
        </div>

        <!-- NOTIFICATIONS -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Notifications</h3>
                <a href="{{ route('buyer.notifications') }}" style="color: #2CB8B4; text-decoration: none; font-size: 14px;">View All →</a>
            </div>
            @if(isset($notifications) && count($notifications) > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach(array_slice($notifications, 0, 5) as $notification)
                        <li style="margin-bottom: 12px; padding: 10px; background: #F9F9F9; border-radius: 6px;">
                            <div style="font-size: 13px; color: #666; margin-bottom: 4px;">
                                @if(isset($notification['date']))
                                    {{ $notification['date']->format('M j, Y g:i A') }}
                                @endif
                            </div>
                            <div style="font-size: 14px;">
                                @if(isset($notification['type']) && $notification['type'] === 'success')
                                    <span style="color: #28a745;">✓</span>
                                @elseif(isset($notification['type']) && $notification['type'] === 'warning')
                                    <span style="color: #ffc107;">⚠</span>
                                @else
                                    <span style="color: #17a2b8;">ℹ</span>
                                @endif
                                {{ $notification['message'] ?? $notification }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No notifications</p>
            @endif
        </div>

        <!-- RECOMMENDED PROPERTIES -->
        @if(isset($recommendedProperties) && $recommendedProperties->count() > 0)
            <div class="card" style="grid-column: 1 / -1;">
                <h3>Recommended Properties for You</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 15px;">
                    @foreach($recommendedProperties as $property)
                        <div style="border: 1px solid var(--line-grey); border-radius: 8px; overflow: hidden; background: var(--white);">
                            @if($property->photos && $property->photos->count() > 0)
                                <div style="width: 100%; height: 180px; background: #F4F4F4; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img src="{{ \Storage::url($property->photos->first()->file_path) }}" 
                                         alt="{{ $property->address }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @else
                                <div style="width: 100%; height: 180px; background: #E8F4F3; display: flex; align-items: center; justify-content: center; color: #666;">
                                    No Image
                                </div>
                            @endif
                            <div style="padding: 15px;">
                                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">{{ Str::limit($property->address, 40) }}</h4>
                                @if($property->asking_price)
                                    <div style="font-size: 18px; font-weight: 700; color: var(--abodeology-teal); margin-bottom: 8px;">
                                        £{{ number_format($property->asking_price, 0) }}
                                    </div>
                                @endif
                                <div style="font-size: 13px; color: #666; margin-bottom: 12px;">
                                    @if($property->bedrooms)
                                        <span>{{ $property->bedrooms }} bed</span>
                                    @endif
                                    @if($property->bathrooms)
                                        <span> • {{ $property->bathrooms }} bath</span>
                                    @endif
                                    @if($property->property_type)
                                        <span> • {{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('buyer.viewing.request', $property->id) }}" class="btn" style="width: 100%; text-align: center; padding: 10px; font-size: 14px;">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
