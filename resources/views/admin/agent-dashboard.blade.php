@extends('layouts.admin')

@section('title', 'Agent Dashboard')

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
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        color: var(--abodeology-teal);
    }

    /* KPI BOXES */
    .kpi-box {
        background: linear-gradient(135deg, var(--abodeology-teal), #25A29F);
        color: var(--white);
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 2px 10px rgba(44, 184, 180, 0.3);
    }

    .kpi-number {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .kpi-label {
        font-size: 15px;
        opacity: 0.9;
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

    .table tr:hover {
        background: #f9f9f9;
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

    /* STATUS BADGES */
    .status {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-sold {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-live {
        background: #d4edda;
        color: #155724;
    }

    /* INFO MESSAGE */
    .info-message {
        background: linear-gradient(135deg, rgba(44, 184, 180, 0.1), rgba(37, 162, 159, 0.1));
        border: 1px solid var(--abodeology-teal);
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 25px;
        color: #1E1E1E;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Agent Dashboard</h2>
    <p class="page-subtitle">Your assigned properties, progress, sales, and tasks.</p>

    @if (session('info'))
        <div class="info-message">
            {{ session('info') }}
        </div>
    @endif

    @if (session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- KPIs -->
    <div class="grid">
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['assigned_properties'] ?? 0 }}</div>
            <div class="kpi-label">My Properties</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['active_listings'] ?? 0 }}</div>
            <div class="kpi-label">Live Listings</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['pending_valuations'] ?? 0 }}</div>
            <div class="kpi-label">Pending Valuations</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['pending_offers'] ?? 0 }}</div>
            <div class="kpi-label">Pending Offers</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['upcoming_viewings'] ?? 0 }}</div>
            <div class="kpi-label">Upcoming Viewings</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-number">{{ $stats['sales_in_progress'] ?? 0 }}</div>
            <div class="kpi-label">Sales Progressing</div>
        </div>
    </div>

    <br><br>

    <!-- MAIN DATA GRID -->
    <div class="grid">
        <!-- TODAY'S APPOINTMENTS -->
        @if(isset($todaysAppointments) && $todaysAppointments->count() > 0)
        <div class="card" style="background: linear-gradient(135deg, rgba(44, 184, 180, 0.1), rgba(37, 162, 159, 0.1));">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">📅 Today's Appointments</h3>
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
                            <a href="{{ route('admin.valuations.show', $appointment->id) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 12px;">View</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif

        <!-- MY PROPERTIES -->
        <div class="card">
            <h3>My Properties</h3>
            <table class="table">
                <tr>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @forelse($properties ?? [] as $property)
                    <tr>
                        <td>{{ Str::limit($property->address, 30) }}</td>
                        <td>
                            <span class="status status-{{ $property->status === 'live' ? 'live' : ($property->status === 'sold' ? 'sold' : 'pending') }}">
                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 12px;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No properties assigned</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.properties.index') }}" class="btn btn-main">View All Properties</a>
        </div>

        <!-- VALUATIONS -->
        <div class="card">
            <h3>Recent Valuations</h3>
            <table class="table">
                <tr>
                    <th>Seller</th>
                    <th>Address</th>
                    <th>Status</th>
                </tr>
                @forelse($valuations ?? [] as $valuation)
                    <tr>
                        <td>{{ $valuation->seller->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($valuation->property_address, 25) }}</td>
                        <td>
                            <span class="status status-{{ $valuation->status === 'pending' ? 'pending' : 'active' }}">
                                {{ ucfirst($valuation->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No valuations</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-main">View All Valuations</a>
        </div>

        <!-- OFFERS -->
        <div class="card">
            <h3>Recent Offers</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                @forelse($offers ?? [] as $offer)
                    <tr>
                        <td>{{ Str::limit($offer->property->address ?? 'N/A', 20) }}</td>
                        <td>{{ $offer->buyer->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($offer->offer_amount ?? 0, 0) }}</td>
                        <td>
                            <span class="status status-{{ $offer->status === 'pending' ? 'pending' : 'active' }}">
                                {{ ucfirst($offer->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No offers</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <!-- VIEWINGS -->
        <div class="card">
            <h3>Upcoming Viewings</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Date</th>
                </tr>
                @forelse($viewings ?? [] as $viewing)
                    @if($viewing->viewing_date >= now())
                        <tr>
                            <td>{{ Str::limit($viewing->property->address ?? 'N/A', 20) }}</td>
                            <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                            <td>{{ $viewing->viewing_date ? $viewing->viewing_date->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No upcoming viewings</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <!-- SALES -->
        @if(($sales ?? collect())->count() > 0)
        <div class="card">
            <h3>Recent Sales</h3>
            <table class="table">
                <tr>
                    <th>Property</th>
                    <th>Seller</th>
                    <th>Status</th>
                </tr>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ Str::limit($sale->address, 30) }}</td>
                        <td>{{ $sale->seller->name ?? 'N/A' }}</td>
                        <td>
                            <span class="status status-sold">Sold</span>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

