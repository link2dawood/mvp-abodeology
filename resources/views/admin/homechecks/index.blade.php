@extends('layouts.admin')

@section('title', 'HomeCheck Reports')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 20px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--line-grey);
    }

    .table th {
        font-weight: 600;
        color: var(--dark-text);
        background: var(--soft-grey);
    }

    .table tr:hover {
        background: #f9f9f9;
    }

    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending { background: #6c757d; color: #fff; }
    .status-scheduled { background: var(--abodeology-teal); color: #fff; }
    .status-in_progress { background: #ffc107; color: #000; }
    .status-completed { background: #28a745; color: #fff; }
    .status-cancelled { background: #dc3545; color: #fff; }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-right: 10px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-secondary {
        background: #666;
        color: #fff;
    }

    .filter-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        align-items: center;
    }

    .filter-bar select {
        padding: 8px 12px;
        border: 1px solid var(--line-grey);
        border-radius: 4px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>HomeCheck Reports</h2>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.homechecks.index') }}" style="display: flex; gap: 10px; align-items: center;">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>

        @if($homechecks->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Seller</th>
                        <th>Status</th>
                        <th>Scheduled Date</th>
                        <th>Completed Date</th>
                        <th>Rooms</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($homechecks as $homecheck)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($homecheck->property->address ?? 'N/A', 30) }}</strong>
                                @if($homecheck->property->postcode)
                                    <br><small style="color: #666;">{{ $homecheck->property->postcode }}</small>
                                @endif
                            </td>
                            <td>{{ $homecheck->property->seller->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status status-{{ $homecheck->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $homecheck->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($homecheck->scheduled_date)
                                    {{ $homecheck->scheduled_date->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($homecheck->completed_at)
                                    {{ $homecheck->completed_at->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not completed</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $roomCount = $homecheck->homecheckData->groupBy('room_name')->count();
                                @endphp
                                {{ $roomCount }} {{ $roomCount === 1 ? 'room' : 'rooms' }}
                            </td>
                            <td>
                                {{ $homecheck->homecheckData->count() }} {{ $homecheck->homecheckData->count() === 1 ? 'image' : 'images' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.homechecks.show', $homecheck->id) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 13px;">View</a>
                                <a href="{{ route('admin.homechecks.edit', $homecheck->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                {{ $homechecks->links() }}
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 40px;">
                No HomeCheck reports found.
            </p>
        @endif
    </div>
</div>
@endsection

