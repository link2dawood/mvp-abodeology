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
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #25A29F;
    }

    .valuations-desktop {
        display: block;
    }

    .valuations-mobile {
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

        h2 {
            font-size: 24px;
        }

        .card {
            padding: 20px;
        }

        .valuations-desktop {
            display: none;
        }

        .valuations-mobile {
            display: block;
        }

        .valuation-mobile-card {
            border: 1px solid var(--line-grey);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.04);
        }

        .valuation-mobile-top {
            margin-bottom: 10px;
        }

        .valuation-mobile-date {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .valuation-mobile-seller {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .valuation-mobile-address {
            color: #374151;
            font-size: 13px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .valuation-mobile-grid {
            border-top: 1px solid #f1f1f1;
            margin-top: 10px;
            padding-top: 8px;
        }

        .valuation-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .valuation-mobile-row:last-child {
            border-bottom: none;
        }

        .valuation-mobile-label {
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            flex: 0 0 40%;
        }

        .valuation-mobile-value {
            color: #1f2937;
            font-size: 13px;
            text-align: right;
            flex: 1;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .valuation-mobile-actions {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
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
        }

        .btn {
            padding: 6px 12px;
            font-size: 13px;
            display: block;
            margin: 0;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .page-subtitle {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 20px;
        }

        .container {
            padding: 0 10px;
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
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
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
        <div class="valuations-desktop">
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
        </div>

        <div class="valuations-mobile">
            @forelse($valuations as $valuation)
                <div class="valuation-mobile-card">
                    <div class="valuation-mobile-top">
                        <div class="valuation-mobile-date">{{ $valuation->created_at->format('M d, Y') }}</div>
                        <div class="valuation-mobile-seller">{{ $valuation->seller->name ?? 'N/A' }}</div>
                        <div class="valuation-mobile-address">{{ $valuation->property_address }}</div>
                    </div>

                    <div class="valuation-mobile-grid">
                        <div class="valuation-mobile-row">
                            <div class="valuation-mobile-label">Type</div>
                            <div class="valuation-mobile-value">{{ $valuation->property_type ? ucfirst(str_replace('_', ' ', $valuation->property_type)) : 'N/A' }}</div>
                        </div>
                        <div class="valuation-mobile-row">
                            <div class="valuation-mobile-label">Bedrooms</div>
                            <div class="valuation-mobile-value">{{ $valuation->bedrooms ?? 'N/A' }}</div>
                        </div>
                        <div class="valuation-mobile-row">
                            <div class="valuation-mobile-label">Valuation Date</div>
                            <div class="valuation-mobile-value">
                                @if($valuation->valuation_date)
                                    {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('M d, Y') }}
                                    @if($valuation->valuation_time)
                                        <br><small>{{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</small>
                                    @endif
                                @else
                                    Not scheduled
                                @endif
                            </div>
                        </div>
                        <div class="valuation-mobile-row">
                            <div class="valuation-mobile-label">Status</div>
                            <div class="valuation-mobile-value">
                                <span class="status status-{{ $valuation->status }}">
                                    {{ ucfirst($valuation->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="valuation-mobile-actions">
                        <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-main">View</a>
                        @if($valuation->status !== 'completed')
                            <a href="{{ route('admin.valuations.onboarding', $valuation->id) }}" class="btn btn-secondary">Complete Onboarding</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #999; padding: 20px 6px;">
                    No valuation requests found
                </div>
            @endforelse
        </div>

        @if($valuations->hasPages())
            <div style="margin-top: 20px;">
                {{ $valuations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

