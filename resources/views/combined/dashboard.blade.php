@extends('layouts.buyer')

@section('title', 'Dashboard')

@push('styles')
<style>
    :root {
        --primary: #32b3ac;
        --primary-dark: #289a94;
        --black: #000;
        --white: #fff;
        --bg: #f5f5f5;
        --text: #111;
        --soft-grey: #e9e9e9;
        --grey: #666;
    }

    body {
        margin: 0;
        background: var(--bg);
        font-family: "Inter", Arial, sans-serif;
        color: var(--text);
    }

    .container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 25px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    /* MODE SWITCH TABS */
    .mode-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 30px;
    }

    .mode-tab {
        padding: 12px 24px;
        border-radius: 8px;
        background: #ddd;
        color: #333;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .mode-tab:hover {
        background: #ccc;
    }

    .mode-tab.active {
        background: var(--primary);
        color: var(--white);
    }

    /* SECTION CARD */
    .section {
        background: var(--white);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 35px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    /* PROPERTY BLOCK */
    .property-block {
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 10px;
        background: #fafafa;
        margin-bottom: 15px;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-instructed { background:#d4edda; color:#155724; }
    .status-marketed { background:#d1edff; color:#004085; }
    .status-offer { background:#fff3cd; color:#856404; }
    .status-live { background:#d1edff; color:#004085; }
    .status-sstc { background:#fff3cd; color:#856404; }

    .btn {
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-primary { 
        background: var(--primary); 
        color: var(--white); 
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-outline { 
        background: var(--white); 
        border: 1px solid var(--primary); 
        color: var(--primary); 
    }

    .btn-outline:hover {
        background: var(--primary);
        color: var(--white);
    }

    /* GRID */
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    @media (max-width: 768px) {
        .two-col {
            grid-template-columns: 1fr;
        }
    }

    /* PROPERTY CARDS */
    .property-card {
        border: 1px solid #eee;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        transition: 0.2s;
    }

    .property-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .property-image {
        height: 160px;
        background-size: cover;
        background-position: center;
        background-color: #ddd;
    }

    .property-content {
        padding: 18px;
    }

    .property-price {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 8px;
    }

    .hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Welcome back, {{ $user->name }}</h2>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- MODE TABS -->
    <div class="mode-tabs">
        <button class="mode-tab active" onclick="switchMode('seller')">Selling My Home</button>
        <button class="mode-tab" onclick="switchMode('buyer')">Buying a Home</button>
    </div>

    <!-- SELLER PANEL -->
    <div id="sellerPanel" class="section">
        <h2>Your Sale</h2>
        @if($primarySellerProperty)
            <div class="property-block">
                <p><strong>{{ $primarySellerProperty->address }}</strong></p>
                @if($primarySellerProperty->postcode)
                    <p style="color: #666; margin: 4px 0;">{{ $primarySellerProperty->postcode }}</p>
                @endif
                <p>Status: 
                    @if($primarySellerProperty->status === 'live')
                        <span class="badge status-marketed">On the Market</span>
                    @elseif($primarySellerProperty->status === 'sstc')
                        <span class="badge status-offer">Sold Subject to Contract</span>
                    @elseif($primarySellerProperty->status === 'signed')
                        <span class="badge status-instructed">Instructed</span>
                    @else
                        <span class="badge">{{ ucfirst($primarySellerProperty->status) }}</span>
                    @endif
                </p>
                <p><strong>Viewings:</strong> {{ $sellerViewings->count() }} booked</p>
                <p><strong>Offers:</strong> {{ $sellerOffers->count() }} active</p>
                <div style="margin-top: 18px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('seller.properties.show', $primarySellerProperty->id) }}" class="btn btn-primary">Manage My Sale</a>
                    @if($primarySellerProperty->homecheckReports && $primarySellerProperty->homecheckReports->where('status', 'completed')->count() > 0)
                        <a href="{{ route('seller.homecheck.report', $primarySellerProperty->id) }}" class="btn btn-outline">View HomeCheck Report</a>
                    @endif
                </div>
            </div>
        @else
            <div class="property-block">
                <p style="color: #666;">You don't have any properties listed yet.</p>
                <p style="color: #666; margin-top: 10px;">Properties are created by agents after your valuation appointment.</p>
            </div>
        @endif
    </div>

    <!-- BUYER PANEL -->
    <div id="buyerPanel" class="section hidden">
        <h2>Your Purchase</h2>
        <p style="color:#555; margin-bottom:15px;">
            Continue your home search and track your buying progress.
        </p>

        <!-- Buyer Grid -->
        <div class="two-col">
            <!-- Viewing Card -->
            @if($upcomingBuyerViewings->count() > 0)
                @php
                    $nextViewing = $upcomingBuyerViewings->first();
                @endphp
                <div class="property-block">
                    <p><strong>Upcoming Viewing</strong></p>
                    <p>{{ $nextViewing->property->address ?? 'N/A' }}</p>
                    @if($nextViewing->viewing_date)
                        <p>{{ \Carbon\Carbon::parse($nextViewing->viewing_date)->format('l, F j') }} at {{ \Carbon\Carbon::parse($nextViewing->viewing_date)->format('H:i') }}</p>
                    @endif
                    <a href="{{ route('buyer.dashboard') }}" class="btn btn-primary" style="margin-top:10px;">View Details</a>
                </div>
            @else
                <div class="property-block">
                    <p><strong>Upcoming Viewing</strong></p>
                    <p style="color: #666;">No upcoming viewings scheduled.</p>
                    <a href="{{ route('buyer.dashboard') }}" class="btn btn-primary" style="margin-top:10px;">Browse Properties</a>
                </div>
            @endif

            <!-- Offer Card -->
            @if($activeBuyerOffer)
                <div class="property-block">
                    <p><strong>Your Active Offer</strong></p>
                    <p>£{{ number_format($activeBuyerOffer->offer_amount, 0) }} — {{ ucfirst($activeBuyerOffer->status) }}</p>
                    <p style="color: #666; font-size: 13px; margin-top: 4px;">{{ $activeBuyerOffer->property->address ?? 'N/A' }}</p>
                    <a href="{{ route('buyer.dashboard') }}" class="btn btn-outline" style="margin-top:10px;">View Offer</a>
                </div>
            @else
                <div class="property-block">
                    <p><strong>Your Active Offer</strong></p>
                    <p style="color: #666;">No active offers.</p>
                    <a href="{{ route('buyer.dashboard') }}" class="btn btn-outline" style="margin-top:10px;">Make an Offer</a>
                </div>
            @endif
        </div>
    </div>

    <!-- RECOMMENDED PROPERTIES -->
    @if($recommendedProperties->count() > 0)
    <div class="section">
        <h2>Recommended for You</h2>
        <div class="two-col">
            @foreach($recommendedProperties->take(2) as $property)
                @php
                    $propertyPhoto = $property->photos && $property->photos->count() > 0 
                        ? \Storage::url($property->photos->first()->image_path) 
                        : 'https://via.placeholder.com/400';
                @endphp
                <div class="property-card">
                    <div class="property-image" style="background-image:url('{{ $propertyPhoto }}');"></div>
                    <div class="property-content">
                        <div class="property-price">£{{ number_format($property->asking_price ?? 0, 0) }}</div>
                        <div>{{ $property->address }}</div>
                        @if($property->postcode)
                            <div style="color: #666; font-size: 13px; margin-top: 4px;">{{ $property->postcode }}</div>
                        @endif
                        <a href="{{ route('buyer.viewing.request', $property->id) }}" class="btn btn-primary" style="margin-top:10px;">Book Viewing</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function switchMode(mode) {
        // Update tab active states
        const tabs = document.querySelectorAll('.mode-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        
        if (mode === 'seller') {
            tabs[0].classList.add('active');
            document.getElementById('sellerPanel').classList.remove('hidden');
            document.getElementById('buyerPanel').classList.add('hidden');
        } else {
            tabs[1].classList.add('active');
            document.getElementById('sellerPanel').classList.add('hidden');
            document.getElementById('buyerPanel').classList.remove('hidden');
        }
    }
</script>
@endpush

