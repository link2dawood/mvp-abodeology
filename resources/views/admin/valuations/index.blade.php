@extends('layouts.admin')

@section('title', 'Valuation Requests')

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

    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending { 
        background: #F4C542; 
        color: #000; 
    }

    .status-scheduled { 
        background: var(--abodeology-teal); 
        color: #FFF; 
    }

    .status-completed { 
        background: #28a745; 
        color: #FFF; 
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

    .btn-secondary {
        background: #6c757d;
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        h2 {
            font-size: 24px;
        }

        .card {
            padding: 20px;
            overflow-x: auto;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            min-width: 600px;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
        }

        .btn {
            padding: 6px 12px;
            font-size: 13px;
            display: block;
            margin: 5px 0;
            text-align: center;
        }

        .page-subtitle {
            font-size: 14px;
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

        .status {
            font-size: 11px;
            padding: 4px 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Valuation Requests</h2>
            <p class="page-subtitle">Manage property valuation requests from sellers</p>
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
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Seller</th>
                    <th>Property Address</th>
                    <th>Type</th>
                    <th>Bedrooms</th>
                    <th>Valuation Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($valuations as $valuation)
                    <tr>
                        <td>{{ $valuation->created_at->format('M d, Y') }}</td>
                        <td>{{ $valuation->seller->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($valuation->property_address, 40) }}</td>
                        <td>{{ $valuation->property_type ? ucfirst(str_replace('_', ' ', $valuation->property_type)) : 'N/A' }}</td>
                        <td>{{ $valuation->bedrooms ?? 'N/A' }}</td>
                        <td>
                            @if($valuation->valuation_date)
                                {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('M d, Y') }}
                                @if($valuation->valuation_time)
                                    <br><small>{{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</small>
                                @endif
                            @else
                                Not scheduled
                            @endif
                        </td>
                        <td>
                            <span class="status status-{{ $valuation->status }}">
                                {{ ucfirst($valuation->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-main">View</a>
                            @if($valuation->status !== 'completed')
                                <a href="{{ route('admin.valuations.onboarding', $valuation->id) }}" class="btn btn-secondary">Complete Onboarding</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999; padding: 40px;">
                            No valuation requests found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($valuations->hasPages())
            <div style="margin-top: 20px;">
                {{ $valuations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

