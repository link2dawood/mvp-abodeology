@extends('layouts.seller')

@section('title', 'My Properties')

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
        margin-bottom: 30px;
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

    .properties-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .property-card {
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        padding: 20px;
        background: #fff;
        transition: box-shadow 0.3s ease;
    }

    .property-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .property-address {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 8px;
        color: #1E1E1E;
    }

    .property-postcode {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .property-details {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 14px;
        color: #666;
    }

    .property-details span {
        display: flex;
        align-items: center;
    }

    .property-status {
        display: inline-block;
        padding: 6px 12px;
        background: #000000;
        color: #fff;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .property-status.draft {
        background: #666;
    }

    .property-status.live {
        background: #2CB8B4;
    }

    .property-status.sold {
        background: #28a745;
    }

    .property-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .property-actions .btn {
        margin: 0;
        font-size: 13px;
        padding: 8px 15px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #1E1E1E;
    }

    .empty-state p {
        margin-bottom: 30px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>My Properties</h2>
        <a href="{{ route('seller.properties.create') }}" class="btn btn-primary">+ Create New Property</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($properties && $properties->count() > 0)
        <div class="properties-grid">
            @foreach($properties as $property)
                <div class="property-card">
                    <div class="property-address">{{ $property->address }}</div>
                    @if($property->postcode)
                        <div class="property-postcode">{{ $property->postcode }}</div>
                    @endif

                    <div class="property-details">
                        @if($property->bedrooms)
                            <span>🛏️ {{ $property->bedrooms }} bed</span>
                        @endif
                        @if($property->bathrooms)
                            <span>🚿 {{ $property->bathrooms }} bath</span>
                        @endif
                        @if($property->property_type)
                            <span>🏠 {{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</span>
                        @endif
                    </div>

                    @if($property->asking_price)
                        <div style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #1E1E1E;">
                            £{{ number_format($property->asking_price, 0) }}
                        </div>
                    @endif

                    <div class="property-status {{ $property->status }}">
                        {{ $property->status_text ?? ucfirst($property->status) }}
                    </div>

                    <div class="property-actions">
                        <a href="{{ route('seller.properties.show', $property->id) }}" class="btn">View Details</a>
                        @if($property->status === 'draft')
                            <a href="{{ route('seller.onboarding', $property->id) }}" class="btn btn-primary">Start Onboarding</a>
                        @elseif($property->status === 'live')
                            <a href="#" class="btn">View Live Listing</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <h3>No properties yet</h3>
            <p>Create your first property listing to get started with selling.</p>
            <a href="{{ route('seller.properties.create') }}" class="btn btn-primary">Create Your First Property</a>
        </div>
    @endif
</div>
@endsection

