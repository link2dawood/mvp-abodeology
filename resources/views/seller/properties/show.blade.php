@extends('layouts.seller')

@section('title', 'Property Details')

@push('styles')
<style>
    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .btn-primary {
        background: #2CB8B4;
    }

    .card {
        border: 1px solid #dcdcdc;
        padding: 20px;
        margin: 20px 0;
        border-radius: 4px;
        background: #fff;
    }

    .property-header {
        margin-bottom: 20px;
    }

    .property-address {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1E1E1E;
    }

    .property-postcode {
        color: #666;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        background: #000000;
        color: #fff;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .status-badge.draft {
        background: #666;
    }

    .status-badge.property_details_completed {
        background: #2CB8B4;
    }

    .status-badge.signed {
        background: #28a745;
    }

    .status-badge.live {
        background: #2CB8B4;
    }

    .status-badge.sold {
        background: #28a745;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        padding: 15px;
        background: #F4F4F4;
        border-radius: 4px;
    }

    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1E1E1E;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 30px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #dcdcdc;
    }

    .action-buttons {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dcdcdc;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Property Details</h2>
        <a href="{{ route('seller.properties.index') }}" class="btn">← Back to Properties</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="property-header">
            <div class="property-address">{{ $property->address }}</div>
            @if($property->postcode)
                <div class="property-postcode">{{ $property->postcode }}</div>
            @endif
            <span class="status-badge {{ $property->status }}">
                {{ $property->status_text ?? ucfirst($property->status) }}
            </span>
        </div>

        <div class="info-grid">
            @if($property->asking_price)
                <div class="info-item">
                    <div class="info-label">Asking Price</div>
                    <div class="info-value">£{{ number_format($property->asking_price, 0) }}</div>
                </div>
            @endif

            @if($property->property_type)
                <div class="info-item">
                    <div class="info-label">Property Type</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</div>
                </div>
            @endif

            @if($property->bedrooms)
                <div class="info-item">
                    <div class="info-label">Bedrooms</div>
                    <div class="info-value">{{ $property->bedrooms }}</div>
                </div>
            @endif

            @if($property->bathrooms)
                <div class="info-item">
                    <div class="info-label">Bathrooms</div>
                    <div class="info-value">{{ $property->bathrooms }}</div>
                </div>
            @endif

            @if($property->tenure)
                <div class="info-item">
                    <div class="info-label">Tenure</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->tenure)) }}</div>
                </div>
            @endif

            @if($property->parking)
                <div class="info-item">
                    <div class="info-label">Parking</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->parking)) }}</div>
                </div>
            @endif
        </div>

        @if($property->lease_years_remaining || $property->ground_rent || $property->service_charge)
            <div class="section-title">Leasehold Information</div>
            <div class="info-grid">
                @if($property->lease_years_remaining)
                    <div class="info-item">
                        <div class="info-label">Lease Years Remaining</div>
                        <div class="info-value">{{ $property->lease_years_remaining }} years</div>
                    </div>
                @endif

                @if($property->ground_rent)
                    <div class="info-item">
                        <div class="info-label">Ground Rent</div>
                        <div class="info-value">£{{ number_format($property->ground_rent, 2) }} per year</div>
                    </div>
                @endif

                @if($property->service_charge)
                    <div class="info-item">
                        <div class="info-label">Service Charge</div>
                        <div class="info-value">£{{ number_format($property->service_charge, 2) }} per year</div>
                    </div>
                @endif

                @if($property->managing_agent)
                    <div class="info-item">
                        <div class="info-label">Managing Agent</div>
                        <div class="info-value">{{ $property->managing_agent }}</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="action-buttons">
            @if($property->status === 'draft')
                <a href="{{ route('seller.onboarding', $property->id) }}" class="btn btn-primary">Start Onboarding</a>
            @elseif($property->status === 'property_details_completed')
                @if($property->instruction && $property->instruction->status === 'pending')
                    <a href="{{ route('seller.instruct', $property->id) }}" class="btn btn-primary">Sign Terms & Conditions</a>
                    <p style="color: #2CB8B4; margin-top: 10px;">✓ Instruction request received. Please sign the Terms & Conditions to proceed.</p>
                @elseif(!$property->instruction || $property->instruction->status !== 'signed')
                    <p style="color: #666; margin-top: 10px;">Waiting for instruction request from your agent...</p>
                @endif
            @elseif($property->status === 'signed')
                <p style="color: #28a745; font-weight: 600; margin-top: 10px;">✓ Terms & Conditions signed. Welcome Pack sent!</p>
            @elseif($property->status === 'live')
                <a href="#" class="btn">View Live Listing</a>
                <a href="{{ route('seller.homecheck.upload', $property->id) }}" class="btn">Manage HomeCheck</a>
            @endif
            <a href="{{ route('seller.properties.index') }}" class="btn" style="background: #666;">Back to Properties</a>
        </div>
    </div>

    @if($property->offers && $property->offers->count() > 0)
        <div class="section-title">Offers ({{ $property->offers->count() }})</div>
        <div class="card">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Buyer</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Amount</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Status</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Action</th>
                </tr>
                @foreach($property->offers as $offer)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">{{ $offer->buyer->name ?? 'N/A' }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">£{{ number_format($offer->offer_amount ?? 0, 0) }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">{{ ucfirst($offer->status ?? 'Pending') }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">
                            @if($offer->status === 'pending')
                                <a href="{{ route('seller.offer.decision', $offer->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">Review Offer</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</div>
@endsection

