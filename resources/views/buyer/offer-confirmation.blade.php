@extends('layouts.buyer')

@section('title', 'Offer Submitted Successfully')

@push('styles')
<style>
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 22px;
    }

    .success-box {
        background: var(--white);
        padding: 40px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
        text-align: center;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: #28a745;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 40px;
        color: white;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #28a745;
    }

    .offer-details {
        background: #E8F4F3;
        padding: 25px;
        border-radius: 8px;
        margin: 30px 0;
        text-align: left;
    }

    .offer-details h3 {
        margin-top: 0;
        color: var(--abodeology-teal);
        font-size: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(44, 184, 180, 0.2);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #1E1E1E;
    }

    .detail-value {
        color: #666;
    }

    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 14px 28px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
        font-size: 15px;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: transparent;
        color: var(--abodeology-teal);
        border: 2px solid var(--abodeology-teal);
    }

    .btn-secondary:hover {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .info-box {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 15px;
        margin: 25px 0;
        color: #155724;
        font-size: 14px;
        text-align: left;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="success-box">
        <div class="success-icon">✓</div>
        
        <h2>Offer Submitted Successfully!</h2>
        
        <p style="font-size: 16px; color: #666; margin-bottom: 30px;">
            Your offer has been received and the seller has been notified. You will receive an email confirmation shortly.
        </p>

        @if(session('success'))
            <div class="info-box">
                {{ session('success') }}
            </div>
        @endif

        <div class="offer-details">
            <h3>Offer Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Property Address:</span>
                <span class="detail-value">{{ $offer->property->address ?? 'N/A' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Your Offer Amount:</span>
                <span class="detail-value" style="font-weight: 600; color: var(--abodeology-teal); font-size: 18px;">
                    £{{ number_format($offer->offer_amount, 2) }}
                </span>
            </div>
            
            @if($offer->property->asking_price)
                <div class="detail-row">
                    <span class="detail-label">Asking Price:</span>
                    <span class="detail-value">£{{ number_format($offer->property->asking_price, 2) }}</span>
                </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Funding Type:</span>
                <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $offer->funding_type ?? 'N/A')) }}</span>
            </div>
            
            @if($offer->deposit_amount)
                <div class="detail-row">
                    <span class="detail-label">Deposit Amount:</span>
                    <span class="detail-value">£{{ number_format($offer->deposit_amount, 2) }}</span>
                </div>
            @endif
            
            @if($offer->chain_position)
                <div class="detail-row">
                    <span class="detail-label">Buying Position:</span>
                    <span class="detail-value">{{ ucfirst(str_replace('-', ' ', $offer->chain_position)) }}</span>
                </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #ffc107; font-weight: 600;">{{ ucfirst($offer->status) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Submitted:</span>
                <span class="detail-value">{{ $offer->created_at->format('l, F j, Y g:i A') }}</span>
            </div>
            
            @if($offer->conditions)
                <div class="detail-row" style="flex-direction: column; align-items: flex-start;">
                    <span class="detail-label" style="margin-bottom: 8px;">Conditions:</span>
                    <span class="detail-value" style="white-space: pre-wrap;">{{ $offer->conditions }}</span>
                </div>
            @endif
        </div>

        <div style="margin-top: 30px;">
            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                The seller and their agent have been notified of your offer. They will review it and respond as soon as possible.
            </p>
            
            <a href="{{ route('buyer.dashboard') }}" class="btn">Go to Dashboard</a>
            <a href="{{ route('buyer.viewing.request', $offer->property->id) }}" class="btn btn-secondary">View Property Details</a>
        </div>
    </div>
</div>
@endsection

