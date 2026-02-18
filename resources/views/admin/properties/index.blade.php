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
        box-sizing: border-box;
        max-width: 100%;
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

    .table td strong,
    .table td span {
        word-break: break-word;
    }

    .properties-desktop {
        display: block;
    }

    .properties-mobile {
        display: none;
    }

    @media (max-width: 900px) {
        .container {
            padding: 0 14px;
            margin: 22px auto;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            margin: 0;
        }
    }

    @media (max-width: 768px) {
        .properties-desktop {
            display: none;
        }

        .properties-mobile {
            display: block;
        }

        .property-mobile-card {
            background: var(--white);
            border: 1px solid var(--line-grey);
            border-radius: 12px;
            box-shadow: 0px 3px 12px rgba(0, 0, 0, 0.05);
            padding: 14px;
            margin-bottom: 12px;
        }

        .property-mobile-address {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .property-mobile-postcode {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .property-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-top: 1px solid #f2f2f2;
            font-size: 13px;
        }

        .property-mobile-row:first-of-type {
            border-top: none;
            padding-top: 0;
        }

        .property-mobile-label {
            color: #4b5563;
            font-weight: 600;
            flex: 0 0 38%;
        }

        .property-mobile-value {
            color: #1f2937;
            text-align: right;
            flex: 1;
            word-break: break-word;
        }

        .property-mobile-actions {
            margin-top: 12px;
        }

        .property-mobile-actions .btn {
            display: block;
            width: 100%;
            text-align: center;
            margin: 0;
        }

        .status {
            font-size: 11px;
            padding: 4px 8px;
        }

        .property-mobile-value .status {
            display: inline-block;
        }

        .btn {
            padding: 9px 14px;
            font-size: 13px;
        }
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

    @if(request('status') === 'live')
        <div style="background: #E8F4F3; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <p style="margin: 0; color: #1E1E1E;"><strong>Showing Live Properties Only</strong></p>
        </div>
    @endif

    @if($properties && $properties->count() > 0)
        <div class="properties-desktop">
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
                            <td data-label="Address">
                                <strong>{{ $property->address }}</strong>
                                @if($property->postcode)
                                    <br><span style="color: #666; font-size: 13px;">{{ $property->postcode }}</span>
                                @endif
                            </td>
                            <td data-label="Seller">
                                {{ $property->seller->name ?? 'N/A' }}
                                @if($property->seller)
                                    <br><span style="color: #666; font-size: 13px;">{{ $property->seller->email }}</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                <span class="status status-{{ $property->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                                </span>
                            </td>
                            <td data-label="Price">
                                @if($property->asking_price)
                                    £{{ number_format($property->asking_price, 0) }}
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td data-label="Created">
                                {{ $property->created_at->format('M d, Y') }}
                            </td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="properties-mobile">
            @foreach($properties as $property)
                <div class="property-mobile-card">
                    <div class="property-mobile-address">{{ $property->address }}</div>
                    @if($property->postcode)
                        <div class="property-mobile-postcode">{{ $property->postcode }}</div>
                    @endif

                    <div class="property-mobile-row">
                        <div class="property-mobile-label">Seller</div>
                        <div class="property-mobile-value">
                            {{ $property->seller->name ?? 'N/A' }}
                            @if($property->seller)
                                <br><span style="color: #6b7280; font-size: 12px;">{{ $property->seller->email }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="property-mobile-row">
                        <div class="property-mobile-label">Status</div>
                        <div class="property-mobile-value">
                            <span class="status status-{{ $property->status }}">
                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="property-mobile-row">
                        <div class="property-mobile-label">Price</div>
                        <div class="property-mobile-value">
                            @if($property->asking_price)
                                £{{ number_format($property->asking_price, 0) }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="property-mobile-row">
                        <div class="property-mobile-label">Created</div>
                        <div class="property-mobile-value">{{ $property->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="property-mobile-actions">
                        <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main">View</a>
                    </div>
                </div>
            @endforeach
        </div>

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

