@extends('layouts.pva')

@section('title', 'Viewing Details')

@push('styles')
<style>
    :root {
        --primary: #32b3ac;
        --primary-dark: #289a94;
        --black: #000;
        --white: #fff;
        --bg: #f7f7f7;
        --card: #ffffff;
        --text: #111;
        --muted: #666;
    }

    .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
    }

    .section {
        background: var(--card);
        border-radius: 14px;
        padding: 30px;
        margin-bottom: 35px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }

    h2 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #e8e8e8;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        width: 200px;
        color: var(--muted);
    }

    .info-value {
        flex: 1;
        color: var(--text);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        margin-right: 10px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        transition: 0.2s;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-success {
        background: #5cb85c;
        color: white;
    }

    .btn-success:hover {
        background: #4cae4c;
    }

    .btn-outline {
        background: white;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-scheduled {
        background: #32b3ac;
        color: white;
    }

    .status-completed {
        background: #5cb85c;
        color: white;
    }

    .status-pending {
        background: #ffc107;
        color: #000;
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <!-- VIEWING OVERVIEW -->
    <div class="section">
        <h2>Viewing Details</h2>
        
        <div class="info-row">
            <div class="info-label">Property Address</div>
            <div class="info-value"><strong>{{ $viewing->property->address ?? 'N/A' }}</strong></div>
        </div>

        <div class="info-row">
            <div class="info-label">Viewing Date & Time</div>
            <div class="info-value">
                {{ $viewing->viewing_date ? $viewing->viewing_date->format('l, F j, Y \a\t g:i A') : 'N/A' }}
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="status-badge status-{{ $viewing->status }}">{{ ucfirst($viewing->status) }}</span>
            </div>
        </div>

        @if($viewing->arrival_time)
            <div class="info-row">
                <div class="info-label">Arrival Time</div>
                <div class="info-value">
                    {{ $viewing->arrival_time->format('g:i A') }} ({{ $viewing->arrival_time->diffForHumans() }})
                </div>
            </div>
        @endif
    </div>

    <!-- BUYER INFORMATION -->
    <div class="section">
        <h2>Buyer Information</h2>
        
        <div class="info-row">
            <div class="info-label">Name</div>
            <div class="info-value">{{ $viewing->buyer->name ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Phone</div>
            <div class="info-value">
                <a href="tel:{{ $viewing->buyer->phone ?? '' }}">{{ $viewing->buyer->phone ?? 'N/A' }}</a>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $viewing->buyer->email ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- ACCESS & INSTRUCTIONS -->
    <div class="section">
        <h2>Access & Instructions</h2>
        
        <div class="info-row">
            <div class="info-label">Access Type</div>
            <div class="info-value"><strong>{{ $accessType ?? 'Keys needed' }}</strong></div>
        </div>

        @if($viewing->access_instructions)
            <div class="info-row">
                <div class="info-label">Access Instructions</div>
                <div class="info-value">{{ $viewing->access_instructions }}</div>
            </div>
        @endif

        @if($viewing->special_instructions)
            <div class="info-row">
                <div class="info-label">Special Instructions</div>
                <div class="info-value" style="color: #d9534f; font-weight: 600;">{{ $viewing->special_instructions }}</div>
            </div>
        @endif
    </div>

    <!-- PROPERTY INFORMATION -->
    <div class="section">
        <h2>Property Information</h2>
        
        <div class="info-row">
            <div class="info-label">Property Type</div>
            <div class="info-value">{{ $viewing->property->property_type ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Bedrooms</div>
            <div class="info-value">{{ $viewing->property->bedrooms ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Bathrooms</div>
            <div class="info-value">{{ $viewing->property->bathrooms ?? 'N/A' }}</div>
        </div>

        @if($viewing->property->parking)
            <div class="info-row">
                <div class="info-label">Parking</div>
                <div class="info-value">{{ $viewing->property->parking }}</div>
            </div>
        @endif
    </div>

    <!-- ACTIONS -->
    <div class="section">
        <h2>Actions</h2>
        
        @if($viewing->status !== 'completed')
            @if(!$viewing->arrival_time)
                <form action="{{ route('pva.viewings.start', $viewing->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Start Viewing / I Have Arrived</button>
                </form>
            @else
                <div class="alert alert-info">
                    Viewing started at {{ $viewing->arrival_time->format('g:i A') }}
                </div>
            @endif

            @if($viewing->pva_id === auth()->id() || $viewing->pva_id === null)
                <a href="{{ route('pva.viewings.feedback', $viewing->id) }}" class="btn btn-primary">Submit Viewing Feedback</a>
            @endif
        @else
            <div class="alert alert-success">
                This viewing has been completed.
                @if($viewing->feedback)
                    <a href="{{ route('pva.viewings.feedback', $viewing->id) }}" class="btn btn-outline" style="margin-top: 10px;">View Feedback</a>
                @endif
            </div>
        @endif

        <a href="{{ route('pva.dashboard') }}" class="btn btn-outline">Back to Dashboard</a>
    </div>
</div>
@endsection

