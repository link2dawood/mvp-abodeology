@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* TITLE */
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 30px;
    }

    /* GRID LAYOUT */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 25px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
    }

    /* KPI BOXES */
    .kpi-box {
        background: var(--white);
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 2px 10px rgba(0,0,0,0.05);
    }

    .kpi-number {
        font-size: 36px;
        font-weight: bold;
        color: var(--abodeology-teal);
        margin-bottom: 5px;
    }

    .kpi-label {
        font-size: 15px;
        color: #666;
    }

    /* TABLES */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
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
        padding: 10px 16px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        margin-top: 10px;
        font-size: 14px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    .btn-dark {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-dark:hover {
        background: #25A29F;
    }

    .btn-danger {
        background: var(--danger);
        color: var(--white);
    }

    .btn-danger:hover {
        background: #C73E3E;
    }

    /* STATUS BADGES */
    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending { 
        background: #F4C542; 
        color: #000; 
    }

    .status-active { 
        background: var(--abodeology-teal); 
        color: #FFF; 
    }

    .status-danger { 
        background: var(--danger); 
        color: #FFF; 
    }

    /* LIST */
    .card ul {
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
    }

    .card ul li {
        padding: 8px 0;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .card ul li:last-child {
        border-bottom: none;
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

        .kpi-box {
            padding: 20px;
        }

        .kpi-number {
            font-size: 28px;
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
            padding: 8px 14px;
            font-size: 13px;
            width: 100%;
            text-align: center;
            margin-top: 8px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 20px;
        }

        .grid {
            gap: 12px;
        }

        .kpi-box {
            padding: 15px;
        }

        .kpi-number {
            font-size: 24px;
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
    <h2>Admin Dashboard</h2>
    <p class="page-subtitle">Complete visibility and control across the entire Abodeology platform.</p>

    <!-- KPIs -->
    <div class="grid">
        <div class="kpi-box" style="background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['total_valuations'] ?? 0 }}</div>
            <div class="kpi-label">Total Valuations</div>
        </div>
        <div class="kpi-box" style="background: linear-gradient(135deg, #fff3cd 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['pending_valuations'] ?? 0 }}</div>
            <div class="kpi-label">Pending Requests</div>
        </div>
        <div class="kpi-box" style="background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['active_listings'] ?? 0 }}</div>
            <div class="kpi-label">Live Listings</div>
        </div>
        <div class="kpi-box" style="background: linear-gradient(135deg, #fff3cd 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['offers_received'] ?? 0 }}</div>
            <div class="kpi-label">Offers Pending</div>
        </div>
        <div class="kpi-box" style="background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['sales_in_progress'] ?? 0 }}</div>
            <div class="kpi-label">Sales Progressing</div>
        </div>
        <div class="kpi-box" style="background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%);">
            <div class="kpi-number">{{ $stats['pvas_active'] ?? 0 }}</div>
            <div class="kpi-label">Active PVAs</div>
        </div>
    </div>

    <br><br>

    <!-- CRITICAL ACTIONS SECTION -->
    <h2 style="margin-top: 40px;">Critical Actions Requiring Attention</h2>
    <div class="grid">
        <!-- AML PENDING -->
        @if(isset($amlPending) && $amlPending->count() > 0)
            <div class="card">
                <h3 style="color: #856404; margin-top: 0;">AML Pending Verification</h3>
                <div style="margin-bottom: 15px; padding: 12px; background: #fff3cd; border-radius: 6px;">
                    <strong style="color: #856404;">{{ $amlPending->count() }} AML check(s) pending</strong>
                </div>
                <table class="table">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                    @foreach($amlPending as $aml)
                        <tr>
                            <td>
                                <strong>{{ $aml->user->name ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $aml->user->email ?? 'N/A' }}</td>
                            <td>
                                <span class="status status-pending">{{ ucfirst($aml->user->role ?? 'N/A') }}</span>
                            </td>
                            <td style="font-size: 12px; color: #666;">
                                {{ $aml->created_at->format('M j, Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.aml-checks.verify', $aml->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px; background: #ffc107; color: #000;">Verify</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <a href="{{ route('admin.aml-checks.index') }}" class="btn btn-main">View All AML Checks</a>
            </div>
        @endif

        <!-- OFFERS PENDING SELLER RESPONSE -->
        @if(isset($offersPendingResponse) && $offersPendingResponse->count() > 0)
            <div class="card">
                <h3 style="color: #856404; margin-top: 0;">Offers Pending Seller Response</h3>
                <div style="margin-bottom: 15px; padding: 12px; background: #fff3cd; border-radius: 6px;">
                    <strong style="color: #856404;">{{ $offersPendingResponse->count() }} offer(s) awaiting seller response</strong>
                </div>
                <table class="table">
                    <tr>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Offer Amount</th>
                        <th>Asking Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @foreach($offersPendingResponse as $offer)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($offer->property->address ?? 'N/A', 20) }}</strong>
                            </td>
                            <td>{{ $offer->buyer->name ?? 'N/A' }}</td>
                            <td>
                                <strong style="color: var(--abodeology-teal);">£{{ number_format($offer->offer_amount, 0) }}</strong>
                            </td>
                            <td>
                                @if($offer->property->asking_price)
                                    £{{ number_format($offer->property->asking_price, 0) }}
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="status status-pending">{{ ucfirst($offer->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.properties.show', $offer->property_id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-main">View All Offers</a>
            </div>
        @endif

        <!-- HOMECHECK PENDING -->
        @if(isset($homecheckPending) && $homecheckPending->count() > 0)
            <div class="card">
                <h3 style="color: #856404; margin-top: 0;">HomeCheck Pending</h3>
                <div style="margin-bottom: 15px; padding: 12px; background: #fff3cd; border-radius: 6px;">
                    <strong style="color: #856404;">{{ $homecheckPending->count() }} HomeCheck(s) pending completion</strong>
                </div>
                <table class="table">
                    <tr>
                        <th>Property</th>
                        <th>Seller</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                    @foreach($homecheckPending as $homecheck)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($homecheck->property->address ?? 'N/A', 25) }}</strong>
                            </td>
                            <td>{{ $homecheck->property->seller->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status status-pending">{{ ucfirst($homecheck->status) }}</span>
                            </td>
                            <td style="font-size: 12px; color: #666;">
                                {{ $homecheck->created_at->format('M j, Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.properties.show', $homecheck->property_id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-main">View All Properties</a>
            </div>
        @endif
    </div>

    <!-- MAIN DATA GRID -->
    <h2 style="margin-top: 40px;">Overview</h2>
    <div class="grid">
        <!-- TODAY'S APPOINTMENTS -->
        @if(isset($todaysAppointments) && $todaysAppointments->count() > 0)
        <div class="card">
            <h3>Today's Appointments</h3>
            <table class="table">
                <tr>
                    <th>Time</th>
                    <th>Property</th>
                    <th>Seller</th>
                    <th>Action</th>
                </tr>
                @foreach($todaysAppointments as $appointment)
                    <tr>
                        <td>
                            @if($appointment->valuation_time)
                                {{ \Carbon\Carbon::parse($appointment->valuation_time)->format('g:i A') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ Str::limit($appointment->property_address, 30) }}</td>
                        <td>{{ $appointment->seller->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.valuations.show', $appointment->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif

        <!-- NEW VALUATIONS -->
        <div class="card">
            <h3>Recent Valuation Requests</h3>
            <table class="table">
                <tr>
                    <th>Seller</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                @forelse($valuations ?? [] as $valuation)
                    <tr>
                        <td>{{ $valuation->seller->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($valuation->property_address, 30) }}</td>
                        <td>
                            <span class="status status-{{ $valuation->status === 'pending' ? 'pending' : ($valuation->status === 'scheduled' ? 'active' : 'active') }}">
                                {{ ucfirst($valuation->status) }}
                            </span>
                        </td>
                        <td>{{ $valuation->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No valuation requests</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-main">View All Valuations</a>
        </div>

        <!-- LIVE PROPERTIES -->
        <div class="card">
            <h3>Live Properties</h3>
            <table class="table">
                <tr>
                    <th>Address</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                @php
                    $liveProperties = \App\Models\Property::with('seller')
                        ->where('status', 'live')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($liveProperties as $property)
                    <tr>
                        <td>{{ Str::limit($property->address, 30) }}</td>
                        <td>{{ $property->seller->name ?? 'N/A' }}</td>
                        <td>
                            @if($property->asking_price)
                                £{{ number_format($property->asking_price, 0) }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('buyer.viewing.request', $property->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;" target="_blank">View Live Listing</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No live properties</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.properties.index', ['status' => 'live']) }}" class="btn btn-main">View All Live Properties</a>
        </div>

        <!-- PROPERTIES -->
        <div class="card">
            <h3>All Properties</h3>
            <table class="table">
                <tr>
                    <th>Address</th>
                    <th>Seller</th>
                    <th>Status</th>
                </tr>
                @php
                    $recentProperties = \App\Models\Property::with('seller')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($recentProperties as $property)
                    <tr>
                        <td>{{ Str::limit($property->address, 30) }}</td>
                        <td>{{ $property->seller->name ?? 'N/A' }}</td>
                        <td>
                            <span class="status status-{{ $property->status === 'draft' ? 'pending' : ($property->status === 'live' ? 'active' : 'active') }}">
                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No properties yet</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.properties.index') }}" class="btn btn-main">View All Properties</a>
        </div>

        <!-- NEW SELLERS -->
        <div class="card">
            <h3>New Seller Onboardings</h3>
            <table class="table">
                <tr>
                    <th>Seller</th>
                    <th>Stage</th>
                </tr>
                @forelse($sellers ?? [] as $seller)
                    <tr>
                        <td>{{ $seller->name ?? 'N/A' }}</td>
                        <td><span class="status status-active">{{ $seller->role ?? 'Seller' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No new sellers</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-main">Manage Sellers</a>
        </div>

        <!-- BUYERS -->
        <div class="card">
            <h3>New Buyer Registrations</h3>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Verification</th>
                </tr>
                @forelse($buyers ?? [] as $buyer)
                    <tr>
                        <td>{{ $buyer->name ?? 'N/A' }}</td>
                        <td>
                            <span class="status {{ $buyer->email_verified_at ? 'status-active' : 'status-pending' }}">
                                {{ $buyer->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No new buyers</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-main">View Buyers</a>
        </div>

        <!-- OFFERS -->
        <div class="card">
            <h3>Offers Received</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Amount</th>
                </tr>
                @forelse($offers ?? [] as $offer)
                    <tr>
                        <td>{{ Str::limit($offer->property->address ?? 'N/A', 25) }}</td>
                        <td>{{ $offer->buyer->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($offer->offer_amount ?? 0, 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No offers received</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-main">Review Offers</a>
        </div>

        <!-- SALES PROGRESSION -->
        <div class="card">
            <h3>Sales Progression Overview</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Progress</th>
                </tr>
                @forelse($sales ?? [] as $sale)
                    <tr>
                        <td>{{ Str::limit($sale->address ?? 'N/A', 30) }}</td>
                        <td><span class="status status-active">Sold</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No sales in progress</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-dark">Open Sales Pipeline</a>
        </div>

        <!-- PVA ACTIVITY -->
        <div class="card">
            <h3>PVA Activity</h3>
            <table class="table">
                <tr>
                    <th>PVA</th>
                    <th>Today</th>
                </tr>
                @forelse($pvas ?? [] as $pva)
                    <tr>
                        <td>{{ $pva->name ?? 'N/A' }}</td>
                        <td>{{ $pva->assigned_viewings_count ?? 0 }} viewings</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No PVA activity</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.pvas.index') }}" class="btn btn-main">Manage PVAs</a>
        </div>

        <!-- SYSTEM ALERTS -->
        <div class="card">
            <h3>System Alerts</h3>
            <ul>
                @forelse($alerts ?? [] as $alert)
                    <li>{{ $alert }}</li>
                @empty
                    <li style="color: #999;">No alerts at this time</li>
                @endforelse
            </ul>
            <a href="#" class="btn btn-danger">View All Alerts</a>
        </div>
    </div>
</div>
@endsection
