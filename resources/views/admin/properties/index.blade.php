@extends('layouts.admin')

@section('title', 'All Properties')

@push('styles')
<style>
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px 15px;
        text-align: left;
        font-size: 14px;
        font-weight: 600;
    }

    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .table tr:hover {
        background: #f9f9f9;
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

    .status {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-draft {
        background: #6c757d;
        color: #fff;
    }

    .status-property_details_captured {
        background: #ffc107;
        color: #000;
    }

    .status-pre_marketing {
        background: #17a2b8;
        color: #fff;
    }

    .status-live {
        background: var(--abodeology-teal);
        color: #fff;
    }

    .status-sold {
        background: #28a745;
        color: #fff;
    }

    .status-sstc {
        background: #ffc107;
        color: #000;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2>All Properties</h2>
            <p style="color: #666; margin-top: 5px;">Manage all properties in the system</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($properties && $properties->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Address</th>
                    <th>Seller</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($properties as $property)
                    <tr>
                        <td>
                            <strong>{{ $property->address }}</strong>
                            @if($property->postcode)
                                <br><span style="color: #666; font-size: 13px;">{{ $property->postcode }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $property->seller->name ?? 'N/A' }}
                            @if($property->seller)
                                <br><span style="color: #666; font-size: 13px;">{{ $property->seller->email }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="status status-{{ $property->status }}">
                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($property->asking_price)
                                £{{ number_format($property->asking_price, 0) }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>
                            {{ $property->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($properties->hasPages())
            <div style="margin-top: 20px;">
                {{ $properties->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px 20px; color: #666;">
            <h3 style="font-size: 20px; margin-bottom: 10px; color: #1E1E1E;">No Properties Found</h3>
            <p>There are no properties in the system yet.</p>
        </div>
    @endif
</div>
@endsection

