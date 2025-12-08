@extends('layouts.pva')

@section('title', 'PVA Dashboard')

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

    body {
        margin: 0;
        background: var(--bg);
        font-family: "Inter", Arial, sans-serif;
        color: var(--text);
    }


    /* CONTAINER */
    .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
    }

    /* SECTION */
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* BIG ACTION BUTTONS */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 35px;
    }

    .action-btn {
        background: var(--primary);
        color: white;
        padding: 20px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        border: none;
        transition: 0.2s;
        text-decoration: none;
        display: block;
    }

    .action-btn:hover {
        background: var(--primary-dark);
    }

    /* VIEWING LIST */
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

    /* BUTTONS */
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

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .action-grid {
            grid-template-columns: 1fr;
        }

        .header {
            padding: 15px 20px;
            flex-wrap: wrap;
        }

        .nav {
            width: 100%;
            margin-top: 15px;
        }

        .nav a {
            margin-left: 0;
            margin-right: 20px;
        }

        .container {
            padding: 0 16px;
            margin: 20px auto;
        }

        .section {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- ACTIONS -->
    <div class="section">
        <h2>Welcome Back, {{ $pvaName ?? auth()->user()->name }}</h2>
        <div class="action-grid">
            <a href="#todays-viewings" class="action-btn">Today's Viewings</a>
            <a href="{{ route('pva.viewings.index') }}" class="action-btn">View Instructions</a>
            <a href="#pending-feedback" class="action-btn">Submit Feedback</a>
        </div>
    </div>

    <!-- TODAY'S VIEWINGS -->
    <div class="section" id="todays-viewings">
        <h2>Today's Viewings</h2>
        @forelse($todaysTasks ?? [] as $task)
            <div class="viewing-card">
                <div class="viewing-header">
                    <span>{{ $task['property'] ?? 'N/A' }}</span>
                    <span>{{ $task['time'] ?? 'N/A' }}</span>
                </div>
                <div class="meta">Buyer: {{ $task['buyer'] ?? 'N/A' }}</div>
                <div class="meta">Phone: {{ $task['buyer_phone'] ?? 'N/A' }}</div>
                <div class="meta">Access: {{ $task['access_type'] ?? 'N/A' }}</div>
                @if($task['special_instructions'])
                    <div class="meta" style="color: #d9534f; font-weight: 600;">Special Instructions: {{ $task['special_instructions'] }}</div>
                @endif
                @if($task['arrival_time'])
                    <div class="meta" style="color: #5cb85c;">Arrived at: {{ \Carbon\Carbon::parse($task['arrival_time'])->format('g:i A') }}</div>
                @endif
                <a href="{{ route('pva.viewings.show', $task['id'] ?? '#') }}" class="btn btn-primary">View Job</a>
                @if(!$task['arrival_time'] && $task['status'] !== 'completed')
                    <form action="{{ route('pva.viewings.start', $task['id']) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="background: #5cb85c;">Start Viewing</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="viewing-card">
                <div class="viewing-header">
                    <span>No viewings scheduled for today</span>
                </div>
            </div>
        @endforelse

        @if(isset($upcomingViewings) && $upcomingViewings->count() > 0)
            <h2 style="margin-top: 30px;">Upcoming Viewings</h2>
            @foreach($upcomingViewings as $viewing)
                <div class="viewing-card">
                    <div class="viewing-header">
                        <span>{{ $viewing['property'] ?? 'N/A' }}</span>
                        <span>{{ $viewing['time'] ?? 'N/A' }}</span>
                    </div>
                    <div class="meta">Buyer: {{ $viewing['buyer'] ?? 'N/A' }}</div>
                    <div class="meta">Phone: {{ $viewing['buyer_phone'] ?? 'N/A' }}</div>
                    <div class="meta">Date: {{ $viewing['date'] ?? 'N/A' }}</div>
                    <div class="meta">Access: {{ $viewing['access_type'] ?? 'N/A' }}</div>
                    @if($viewing['special_instructions'])
                        <div class="meta" style="color: #d9534f; font-weight: 600;">Special Instructions: {{ $viewing['special_instructions'] }}</div>
                    @endif
                    <a href="{{ route('pva.viewings.show', $viewing['id'] ?? '#') }}" class="btn btn-primary">View Job</a>
                </div>
            @endforeach
        @endif
    </div>

    <!-- SUBMIT FEEDBACK -->
    <div class="section" id="pending-feedback">
        <h2>Pending Feedback</h2>
        @forelse($completedViewings ?? [] as $viewing)
            @if(!$viewing['report'])
                <div class="viewing-card">
                    <div class="viewing-header">
                        <span>{{ $viewing['property'] ?? 'N/A' }}</span>
                        <span>{{ $viewing['date'] ?? 'N/A' }}</span>
                    </div>
                    <div class="meta">Buyer: {{ $viewing['buyer'] ?? 'N/A' }}</div>
                    <a href="{{ route('pva.viewings.feedback', $viewing['id'] ?? '#') }}" class="btn btn-primary">Submit Feedback</a>
                </div>
            @endif
        @empty
            <div class="viewing-card">
                <div class="viewing-header">
                    <span>No pending feedback</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- UNASSIGNED VIEWINGS -->
    @if(isset($unassignedViewings) && $unassignedViewings->count() > 0)
        <div class="section">
            <h2>Available Viewings to Claim</h2>
            @foreach($unassignedViewings as $viewing)
                <div class="viewing-card">
                    <div class="viewing-header">
                        <span>{{ $viewing->property->address ?? 'N/A' }}</span>
                        <span>{{ $viewing->viewing_date ? $viewing->viewing_date->format('g:i A') : 'N/A' }}</span>
                    </div>
                    <div class="meta">Buyer: {{ $viewing->buyer->name ?? 'N/A' }}</div>
                    <div class="meta">{{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y') : 'N/A' }}</div>
                    <form action="{{ route('pva.viewings.confirm', $viewing->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Claim Viewing</button>
                    </form>
                    <a href="{{ route('pva.viewings.show', $viewing->id) }}" class="btn btn-outline">View Details</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
