@extends('layouts.pva')

@section('title', 'PVA Dashboard')

@push('styles')
<style>
    /* CONTAINER */
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    /* GRID */
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
        font-weight: 600;
    }

    .card p {
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.6;
    }

    .card p strong {
        font-weight: 600;
    }

    .card ul {
        padding-left: 20px;
        margin: 15px 0;
    }

    .card ul li {
        margin-bottom: 8px;
        font-size: 14px;
        line-height: 1.6;
    }

    /* TABLE */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
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

    /* BUTTONS */
    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
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

    /* STATUS BADGE */
    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-upcoming { 
        background: var(--abodeology-teal); 
        color: var(--white); 
    }

    .status-completed { 
        background: #0F8F0F; 
        color: var(--white); 
    }

    .status-noshow { 
        background: var(--danger); 
        color: var(--white); 
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
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 20px;
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
    <h2>Welcome back, {{ $pvaName ?? auth()->user()->name }}</h2>
    <p class="page-subtitle">Here are your upcoming viewings and tasks.</p>

    <div class="grid">
        <!-- UPCOMING VIEWINGS -->
        <div class="card">
            <h3>Upcoming Viewings</h3>
            <table class="table">
                <tr>
                    <th>Date</th>
                    <th>Property</th>
                    <th>Status</th>
                </tr>
                @forelse($upcomingViewings ?? [] as $viewing)
                    <tr>
                        <td>
                            <strong>{{ $viewing['date'] ?? 'N/A' }}</strong>
                            <br><span style="font-size: 12px; color: #666;">{{ $viewing['time'] ?? '' }}</span>
                        </td>
                        <td>
                            <strong>{{ $viewing['property'] ?? 'N/A' }}</strong>
                            <br><span style="font-size: 12px; color: #666;">Buyer: {{ $viewing['buyer'] ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="status status-upcoming">{{ $viewing['status'] ?? 'Upcoming' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No upcoming viewings</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('pva.viewings.index') }}" class="btn btn-main">View All Viewings</a>
        </div>

        <!-- TODAY'S TASKS -->
        <div class="card">
            <h3>Today's Tasks</h3>
            <table class="table">
                <tr>
                    <th>Time</th>
                    <th>Property</th>
                </tr>
                @forelse($todaysTasks ?? [] as $task)
                    <tr>
                        <td>
                            <strong>{{ $task['time'] ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <strong>{{ $task['property'] ?? 'N/A' }}</strong>
                            <br><span style="font-size: 12px; color: #666;">{{ $task['buyer'] ?? 'N/A' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No viewings scheduled for today</td>
                    </tr>
                @endforelse
            </table>
            @if(isset($todaysTasks) && $todaysTasks->count() > 0)
                <a href="{{ route('pva.viewings.index') }}" class="btn btn-dark">View Today's Schedule</a>
            @endif
        </div>

        <!-- UNASSIGNED VIEWINGS -->
        @if(isset($unassignedViewings) && $unassignedViewings->count() > 0)
            <div class="card" style="grid-column: 1 / -1; background: #fff3cd; border: 2px solid #ffc107;">
                <h3 style="color: #856404;">Available Viewings to Claim</h3>
                <p style="color: #856404; margin-bottom: 15px;">You can claim these unassigned viewings:</p>
                <table class="table">
                    <tr>
                        <th>Date & Time</th>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Action</th>
                    </tr>
                    @foreach($unassignedViewings as $viewing)
                        <tr>
                            <td>
                                <strong>{{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}</strong>
                            </td>
                            <td>{{ $viewing->property->address ?? 'N/A' }}</td>
                            <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                            <td>
                                <form action="{{ route('pva.viewings.confirm', $viewing->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-main" style="padding: 6px 12px; font-size: 13px;">Claim Viewing</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <!-- VIEWING DETAILS / FORM LINK -->
        <div class="card">
            <h3>Submit Viewing Feedback</h3>
            <p>After completing a viewing, please submit the buyer's:</p>
            <ul>
                <li>Interest Level</li>
                <li>Buyer Feedback</li>
                <li>Property Condition</li>
                <li>PVA Notes</li>
            </ul>
            @if(isset($completedViewings) && $completedViewings->count() > 0)
                <p style="margin-top: 15px; font-size: 13px; color: #666;">
                    <strong>Quick Access:</strong> Select a completed viewing below to submit feedback.
                </p>
            @endif
        </div>

        <!-- COMPLETED VIEWINGS -->
        <div class="card">
            <h3>Completed Viewings</h3>
            <table class="table">
                <tr>
                    <th>Date</th>
                    <th>Property</th>
                    <th>Report</th>
                </tr>
                @forelse($completedViewings ?? [] as $viewing)
                    <tr>
                        <td>{{ $viewing['date'] ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $viewing['property'] ?? 'N/A' }}</strong>
                            <br><span style="font-size: 12px; color: #666;">{{ $viewing['buyer'] ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if(isset($viewing['report']) && $viewing['report'])
                                <a href="{{ route('pva.viewings.show', $viewing['id']) }}" style="color: var(--abodeology-teal); font-weight: 600;">View Feedback</a>
                            @else
                                <a href="{{ route('pva.viewings.feedback', $viewing['id']) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 13px;">Submit Feedback</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No completed viewings</td>
                    </tr>
                @endforelse
            </table>
            <a href="{{ route('pva.viewings.index') }}" class="btn btn-main">View All Viewings</a>
        </div>

        <!-- PROFILE SUMMARY -->
        <div class="card">
            <h3>Your Profile</h3>
            <p><strong>Name:</strong> {{ $pvaName ?? auth()->user()->name }}</p>
            <p><strong>Area(s):</strong> {{ $pvaAreas ?? 'London, Surrey' }}</p>
            <p><strong>Jobs completed:</strong> {{ $jobsCompletedCount ?? 0 }}</p>
            <a href="{{ route('profile.show') }}" class="btn btn-main">Edit Profile</a>
        </div>
    </div>
</div>
@endsection
