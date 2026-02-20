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

    .report-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }
    .report-badge.report-yes {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .report-badge.report-yes:hover {
        background: #c3e6cb;
    }
    .report-badge.report-no {
        color: #999;
        background: #f5f5f5;
    }

    .ai-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 12px;
    }
    .ai-status.connected {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .ai-status.fallback {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
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

    .homechecks-desktop {
        display: block;
    }

    .homechecks-mobile {
        display: none;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .container {
            padding: 0 12px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .card {
            padding: 18px;
        }

        .filter-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .filter-bar form {
            width: 100%;
            flex-direction: column;
            align-items: stretch !important;
            gap: 8px !important;
        }

        .filter-bar label {
            font-size: 13px;
            font-weight: 600;
        }

        .filter-bar select {
            width: 100%;
        }

        .homechecks-desktop {
            display: none;
        }

        .homechecks-mobile {
            display: block;
        }

        .homecheck-mobile-card {
            border: 1px solid var(--line-grey);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.04);
        }

        .homecheck-mobile-address {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            word-break: break-word;
        }

        .homecheck-mobile-postcode {
            color: #6b7280;
            font-size: 12px;
            margin: 4px 0 10px;
        }

        .homecheck-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-top: 1px solid #f2f2f2;
        }

        .homecheck-mobile-label {
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            flex: 0 0 40%;
        }

        .homecheck-mobile-value {
            color: #1f2937;
            font-size: 13px;
            text-align: right;
            flex: 1;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .homecheck-mobile-actions {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn {
            width: 100%;
            margin: 0;
            text-align: center;
            box-sizing: border-box;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 0 10px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
            <h2 style="margin: 0;">HomeCheck Reports</h2>
            <span class="ai-status {{ ($aiConfigured ?? false) ? 'connected' : 'fallback' }}" title="{{ ($aiConfigured ?? false) ? 'OpenAI API is configured. Analysis will use real AI.' : 'OpenAI not configured. Analysis will use fallback (simulated).' }}">
                {{ ($aiConfigured ?? false) ? '✓ AI connected' : '○ AI fallback' }}
            </span>
        </div>
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
            <div class="homechecks-desktop">
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
                            <th>AI Report</th>
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
                                    @if($homecheck->report_path)
                                        <a href="{{ route('admin.homechecks.show', $homecheck->id) }}" class="report-badge report-yes" title="View AI report">✓ Report</a>
                                    @else
                                        <span class="report-badge report-no">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.homechecks.show', $homecheck->id) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 13px;">View</a>
                                    <a href="{{ route('admin.homechecks.edit', $homecheck->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="homechecks-mobile">
                @foreach($homechecks as $homecheck)
                    @php
                        $roomCount = $homecheck->homecheckData->groupBy('room_name')->count();
                        $imageCount = $homecheck->homecheckData->count();
                    @endphp
                    <div class="homecheck-mobile-card">
                        <div class="homecheck-mobile-address">{{ $homecheck->property->address ?? 'N/A' }}</div>
                        @if($homecheck->property && $homecheck->property->postcode)
                            <div class="homecheck-mobile-postcode">{{ $homecheck->property->postcode }}</div>
                        @endif

                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Seller</div>
                            <div class="homecheck-mobile-value">{{ $homecheck->property->seller->name ?? 'N/A' }}</div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Status</div>
                            <div class="homecheck-mobile-value">
                                <span class="status status-{{ $homecheck->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $homecheck->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Scheduled</div>
                            <div class="homecheck-mobile-value">
                                @if($homecheck->scheduled_date)
                                    {{ $homecheck->scheduled_date->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Completed</div>
                            <div class="homecheck-mobile-value">
                                @if($homecheck->completed_at)
                                    {{ $homecheck->completed_at->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not completed</span>
                                @endif
                            </div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Rooms</div>
                            <div class="homecheck-mobile-value">{{ $roomCount }} {{ $roomCount === 1 ? 'room' : 'rooms' }}</div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">Images</div>
                            <div class="homecheck-mobile-value">{{ $imageCount }} {{ $imageCount === 1 ? 'image' : 'images' }}</div>
                        </div>
                        <div class="homecheck-mobile-row">
                            <div class="homecheck-mobile-label">AI Report</div>
                            <div class="homecheck-mobile-value">
                                @if($homecheck->report_path)
                                    <a href="{{ route('admin.homechecks.show', $homecheck->id) }}" class="report-badge report-yes">✓ Report</a>
                                @else
                                    <span class="report-badge report-no">—</span>
                                @endif
                            </div>
                        </div>

                        <div class="homecheck-mobile-actions">
                            <a href="{{ route('admin.homechecks.show', $homecheck->id) }}" class="btn btn-main">View</a>
                            <a href="{{ route('admin.homechecks.edit', $homecheck->id) }}" class="btn btn-secondary">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>

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

