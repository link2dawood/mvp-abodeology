@extends('layouts.admin')

@section('title', 'Manage PVAs')

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

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Manage PVAs</h2>
            <p class="page-subtitle">View and manage Property Viewing Assistants</p>
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Active Viewings</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pvas as $pva)
                    <tr>
                        <td>{{ $pva->name }}</td>
                        <td>{{ $pva->email }}</td>
                        <td>{{ $pva->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-success">{{ $pva->assigned_viewings_count ?? 0 }}</span>
                        </td>
                        <td>{{ $pva->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 40px;">
                            No PVAs found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($pvas->hasPages())
            <div style="margin-top: 20px;">
                {{ $pvas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
