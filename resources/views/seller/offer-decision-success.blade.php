@extends('layouts.seller')

@section('title', 'Decision Recorded')

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
        background: var(--abodeology-teal);
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
        color: var(--abodeology-teal);
    }

    .decision-details {
        background: #E8F4F3;
        padding: 25px;
        border-radius: 8px;
        margin: 30px 0;
        text-align: left;
    }

    .decision-details h3 {
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

    .decision-badge {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-block;
        font-size: 16px;
    }

    .decision-accepted {
        background: #28a745;
        color: white;
    }

    .decision-declined {
        background: #dc3545;
        color: white;
    }

    .decision-counter {
        background: #17a2b8;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="success-box">
        <div class="success-icon">✓</div>
        
        <h2>Decision Recorded Successfully!</h2>
        
        @if(session('success'))
            <div class="info-box">
                {{ session('success') }}
            </div>
        @endif

        @if($offer->latestDecision)
            <div class="decision-details">
                <h3>Decision Summary</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Property:</span>
                    <span class="detail-value">{{ $offer->property->address ?? 'N/A' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Buyer:</span>
                    <span class="detail-value">{{ $offer->buyer->name ?? 'N/A' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Original Offer:</span>
                    <span class="detail-value">£{{ number_format($offer->offer_amount, 2) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Your Decision:</span>
                    <span class="detail-value">
                        @if($offer->latestDecision->decision === 'accepted')
                            <span class="decision-badge decision-accepted">✓ Accepted</span>
                        @elseif($offer->latestDecision->decision === 'declined')
                            <span class="decision-badge decision-declined">✗ Declined</span>
                        @elseif($offer->latestDecision->decision === 'counter')
                            <span class="decision-badge decision-counter">💬 Counter-Offer</span>
                        @endif
                    </span>
                </div>
                
                @if($offer->latestDecision->decision === 'counter' && $offer->latestDecision->counter_amount)
                    <div class="detail-row">
                        <span class="detail-label">Counter-Offer Amount:</span>
                        <span class="detail-value" style="font-weight: 600; color: var(--abodeology-teal); font-size: 18px;">
                            £{{ number_format($offer->latestDecision->counter_amount, 2) }}
                        </span>
                    </div>
                @endif
                
                @if($offer->latestDecision->comments)
                    <div class="detail-row" style="flex-direction: column; align-items: flex-start;">
                        <span class="detail-label" style="margin-bottom: 8px;">Your Comments:</span>
                        <span class="detail-value" style="white-space: pre-wrap;">{{ $offer->latestDecision->comments }}</span>
                    </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Decision Date:</span>
                    <span class="detail-value">{{ $offer->latestDecision->decided_at->format('l, F j, Y g:i A') }}</span>
                </div>
                
                @if($offer->latestDecision->decision === 'accepted')
                    <div class="detail-row">
                        <span class="detail-label">Property Status:</span>
                        <span class="detail-value" style="color: #28a745; font-weight: 600;">Sold Subject to Contract (SSTC)</span>
                    </div>
                @endif
            </div>

            @if($offer->latestDecision->decision === 'accepted')
                <div style="margin-top: 30px; padding: 20px; background: #E8F4F3; border-radius: 8px; text-align: left;">
                    <h3 style="margin-top: 0; color: var(--abodeology-teal);">What Happens Next?</h3>
                    <ul style="margin: 10px 0; padding-left: 25px;">
                        <li>A Memorandum of Sale has been automatically generated</li>
                        <li>The Memorandum has been sent to both solicitors</li>
                        <li>Sales progression workflow has begun</li>
                        <li>The property status has been updated to "Sold Subject to Contract"</li>
                    </ul>
                </div>
            @endif
        @endif

        <div style="margin-top: 30px;">
            <a href="{{ route('seller.properties.show', $offer->property_id) }}" class="btn">View Property</a>
            <a href="{{ route('seller.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection

