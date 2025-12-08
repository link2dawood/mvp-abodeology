@extends('layouts.pva')

@section('title', 'My Viewings')

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

    .viewing-card {
        background: #fafafa;
        border: 1px solid #e8e8e8;
        padding: 22px;
        border-radius: 12px;
        margin-bottom: 18px;
    }

    .viewing-header {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .meta {
        font-size: 14px;
        color: var(--muted);
        margin-bottom: 6px;
    }

    .btn {
        padding: 10px 16px;
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

    .btn-outline {
        background: white;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    .status-scheduled {
        background: #32b3ac;
        color: white;
    }

    .status-pending {
        background: #ffc107;
        color: #000;
    }

    .status-completed {
        background: #5cb85c;
        color: white;
    }

    .status-cancelled {
        background: #d9534f;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--muted);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="section">
        <h2>My Viewings</h2>

        <!-- ASSIGNED VIEWINGS -->
        @if(isset($assignedViewings) && $assignedViewings->count() > 0)
            <h3 style="font-size: 18px; margin-bottom: 15px; margin-top: 20px;">Assigned Viewings</h3>
            @foreach($assignedViewings as $viewing)
                @php
                    // Determine access type
                    $accessType = 'Keys needed';
                    if ($viewing->property && $viewing->property->with_keys) {
                        $accessType = 'Keys available';
                    } elseif ($viewing->access_instructions) {
                        if (stripos($viewing->access_instructions, 'vendor') !== false || stripos($viewing->access_instructions, 'seller') !== false) {
                            $accessType = 'Vendor will open';
                        } elseif (stripos($viewing->access_instructions, 'agent') !== false) {
                            $accessType = 'Agent will open';
                        }
                    }
                @endphp
                <div class="viewing-card">
                    <div class="viewing-header">
                        <span>{{ $viewing->property->address ?? 'N/A' }}</span>
                        <span>
                            {{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}
                            <span class="status-badge status-{{ $viewing->status }}">{{ ucfirst($viewing->status) }}</span>
                        </span>
                    </div>
                    <div class="meta">Buyer: {{ $viewing->buyer->name ?? 'N/A' }}</div>
                    <div class="meta">Phone: {{ $viewing->buyer->phone ?? 'N/A' }}</div>
                    <div class="meta">Access: {{ $accessType }}</div>
                    @if($viewing->special_instructions)
                        <div class="meta" style="color: #d9534f; font-weight: 600;">Special Instructions: {{ $viewing->special_instructions }}</div>
                    @endif
                    @if($viewing->arrival_time)
                        <div class="meta" style="color: #5cb85c;">Arrived at: {{ $viewing->arrival_time->format('g:i A') }}</div>
                    @endif
                    <a href="{{ route('pva.viewings.show', $viewing->id) }}" class="btn btn-primary">View Job</a>
                    @if(!$viewing->arrival_time && $viewing->status !== 'completed')
                        <form action="{{ route('pva.viewings.start', $viewing->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: #5cb85c;">Start Viewing</button>
                        </form>
                    @endif
                    @if($viewing->status === 'completed' && !$viewing->feedback)
                        <a href="{{ route('pva.viewings.feedback', $viewing->id) }}" class="btn btn-outline">Submit Feedback</a>
                    @endif
                </div>
            @endforeach
        @endif

        <!-- UNASSIGNED VIEWINGS -->
        @if(isset($unassignedViewings) && $unassignedViewings->count() > 0)
            <h3 style="font-size: 18px; margin-bottom: 15px; margin-top: 30px;">Available Viewings to Claim</h3>
            @foreach($unassignedViewings as $viewing)
                @php
                    // Determine access type
                    $accessType = 'Keys needed';
                    if ($viewing->property && $viewing->property->with_keys) {
                        $accessType = 'Keys available';
                    } elseif ($viewing->access_instructions) {
                        if (stripos($viewing->access_instructions, 'vendor') !== false || stripos($viewing->access_instructions, 'seller') !== false) {
                            $accessType = 'Vendor will open';
                        } elseif (stripos($viewing->access_instructions, 'agent') !== false) {
                            $accessType = 'Agent will open';
                        }
                    }
                @endphp
                <div class="viewing-card" style="background: #fff3cd; border: 2px solid #ffc107;">
                    <div class="viewing-header">
                        <span>{{ $viewing->property->address ?? 'N/A' }}</span>
                        <span>
                            {{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}
                            <span class="status-badge status-{{ $viewing->status }}">{{ ucfirst($viewing->status) }}</span>
                        </span>
                    </div>
                    <div class="meta">Buyer: {{ $viewing->buyer->name ?? 'N/A' }}</div>
                    <div class="meta">Phone: {{ $viewing->buyer->phone ?? 'N/A' }}</div>
                    <div class="meta">Access: {{ $accessType }}</div>
                    @if($viewing->special_instructions)
                        <div class="meta" style="color: #d9534f; font-weight: 600;">Special Instructions: {{ $viewing->special_instructions }}</div>
                    @endif
                    <form action="{{ route('pva.viewings.confirm', $viewing->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Claim Viewing</button>
                    </form>
                    <a href="{{ route('pva.viewings.show', $viewing->id) }}" class="btn btn-outline">View Details</a>
                </div>
            @endforeach
        @endif

        <!-- EMPTY STATE -->
        @if((!isset($assignedViewings) || $assignedViewings->count() === 0) && (!isset($unassignedViewings) || $unassignedViewings->count() === 0))
            <div class="empty-state">
                <p>No viewings found.</p>
                <a href="{{ route('pva.dashboard') }}" class="btn btn-primary" style="margin-top: 20px;">Back to Dashboard</a>
            </div>
        @endif
    </div>
</div>
@endsection

