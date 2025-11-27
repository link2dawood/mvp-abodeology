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
                        <td>{{ $viewing['date'] ?? 'N/A' }}</td>
                        <td>{{ $viewing['property'] ?? 'N/A' }}</td>
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
            <a href="#" class="btn btn-main">View All</a>
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
                        <td>{{ $task['time'] ?? 'N/A' }}</td>
                        <td>{{ $task['property'] ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No tasks for today</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-dark">Download Day Sheet</a>
        </div>

        <!-- VIEWING DETAILS / FORM LINK -->
        <div class="card">
            <h3>Submit Viewing Feedback</h3>
            <p>After completing a viewing, please submit the buyer's:</p>
            <ul>
                <li>Name</li>
                <li>Interest Level</li>
                <li>Offer Intentions</li>
                <li>Viewing Feedback</li>
            </ul>
            <a href="#" class="btn btn-main">Open Feedback Form</a>
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
                        <td>{{ $viewing['property'] ?? 'N/A' }}</td>
                        <td>
                            @if(isset($viewing['report']) && $viewing['report'])
                                <a href="#" style="color: var(--abodeology-teal);">View</a>
                            @else
                                <span style="color: #999;">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No completed viewings</td>
                    </tr>
                @endforelse
            </table>
            <a href="#" class="btn btn-main">View All Completed</a>
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
