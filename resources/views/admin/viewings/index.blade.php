@extends('layouts.admin')

@section('title', 'Manage Viewings')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
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
        margin-bottom: 15px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px;
        text-align: left;
        font-size: 14px;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:hover {
        background: #f9f9f9;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #25A29F;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-scheduled {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Manage Viewings</h2>
            <p class="page-subtitle">Assign viewings (jobs) to PVAs</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Viewing Date</th>
                    <th>Status</th>
                    <th>Assigned PVA</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($viewings as $viewing)
                    <tr>
                        <td>{{ $viewing->property->address ?? 'N/A' }}</td>
                        <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                        <td>{{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}</td>
                        <td>
                            <span class="status-badge status-{{ $viewing->status }}">{{ ucfirst($viewing->status) }}</span>
                        </td>
                        <td>{{ $viewing->pva->name ?? 'Unassigned' }}</td>
                        <td>
                            <a href="{{ route('admin.viewings.assign', $viewing->id) }}" class="btn btn-primary">Assign PVA</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999; padding: 40px;">
                            No viewings found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($viewings->hasPages())
            <div style="margin-top: 20px;">
                {{ $viewings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
