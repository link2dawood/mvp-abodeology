@extends('layouts.admin')

@section('title', 'Valuation Details')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        color: var(--abodeology-teal);
    }

    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        min-width: 200px;
        color: #666;
    }

    .info-value {
        flex: 1;
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
        padding: 10px 20px;
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
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Valuation Request Details</h2>
        </div>
        <div>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-secondary">Back to List</a>
            @if($valuation->status !== 'completed')
                <a href="{{ route('admin.valuations.onboarding', $valuation->id) }}" class="btn btn-main">Complete Onboarding</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h3>Valuation Information</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $valuation->status }}">
                    {{ ucfirst($valuation->status) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Property Address:</div>
            <div class="info-value">{{ $valuation->property_address }}</div>
        </div>
        @if($valuation->postcode)
        <div class="info-row">
            <div class="info-label">Postcode:</div>
            <div class="info-value">{{ $valuation->postcode }}</div>
        </div>
        @endif
        @if($valuation->property_type)
        <div class="info-row">
            <div class="info-label">Property Type:</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $valuation->property_type)) }}</div>
        </div>
        @endif
        @if($valuation->bedrooms)
        <div class="info-row">
            <div class="info-label">Bedrooms:</div>
            <div class="info-value">{{ $valuation->bedrooms }}</div>
        </div>
        @endif
        @if($valuation->valuation_date)
        <div class="info-row">
            <div class="info-label">Preferred Valuation Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}</div>
        </div>
        @endif
        @if($valuation->valuation_time)
        <div class="info-row">
            <div class="info-label">Preferred Valuation Time:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</div>
        </div>
        @endif
        @if($valuation->estimated_value)
        <div class="info-row">
            <div class="info-label">Estimated Value:</div>
            <div class="info-value">£{{ number_format($valuation->estimated_value, 2) }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Request Date:</div>
            <div class="info-value">{{ $valuation->created_at->format('l, F j, Y g:i A') }}</div>
        </div>
    </div>

    <div class="card">
        <h3>Client Information</h3>
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $valuation->seller->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $valuation->seller->email ?? 'N/A' }}</div>
        </div>
        @if($valuation->seller->phone)
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $valuation->seller->phone }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Role:</div>
            <div class="info-value">{{ ucfirst($valuation->seller->role ?? 'N/A') }}</div>
        </div>
    </div>

    @if($valuation->seller_notes)
    <div class="card">
        <h3>Notes from Client</h3>
        <p>{{ $valuation->seller_notes }}</p>
    </div>
    @endif

    @if($valuation->id_visual_check)
    <div class="card" style="background: #d4edda; border-left: 4px solid #28a745;">
        <h3 style="color: #28a745; margin-top: 0;">✓ ID Visual Check Completed</h3>
        <p style="margin: 5px 0;"><strong>Status:</strong> ID document visually checked on-site</p>
        @if($valuation->id_visual_check_notes)
        <p style="margin: 5px 0;"><strong>Notes:</strong> {{ $valuation->id_visual_check_notes }}</p>
        @endif
    </div>
    @endif

    @if($valuation->notes)
    <div class="card">
        <h3>Agent Notes</h3>
        <p>{{ $valuation->notes }}</p>
    </div>
    @endif

    @if($valuation->status !== 'completed')
    <div class="card" style="background: #E8F4F3; border-left: 4px solid var(--abodeology-teal);">
        <h3 style="color: var(--abodeology-teal); margin-top: 0;">Valuation Form</h3>
        <p>Complete the Valuation Form during the on-site valuation appointment. The form will be pre-filled with seller and property details.</p>
        <p><strong>This form captures:</strong></p>
        <ul>
            <li>ID Visual Check confirmation (required by HMRC/EA Act)</li>
            <li>Detailed property information</li>
            <li>Material information</li>
            <li>Access notes and viewing preferences</li>
            <li>Pricing notes</li>
        </ul>
        <p style="font-size: 13px; color: #666; margin-top: 10px;">
            <em>Form will be saved directly to the seller's profile with status "Property Details Captured".</em>
        </p>
        <a href="{{ route('admin.valuations.valuation-form', $valuation->id) }}" class="btn btn-main" style="margin-top: 15px;">Start Valuation Form</a>
        @php
            // Check if property already exists for this valuation
            $existingProperty = \App\Models\Property::where('seller_id', $valuation->seller_id)
                ->where('address', $valuation->property_address)
                ->first();
        @endphp
        @if($existingProperty)
            <a href="{{ route('admin.properties.show', $existingProperty->id) }}" class="btn" style="background: #6c757d; margin-top: 15px; margin-left: 10px;">View Property</a>
        @endif
    </div>
    @else
    <div class="card" style="background: #d4edda; border-left: 4px solid #28a745;">
        <h3 style="color: #28a745; margin-top: 0;">✓ Valuation Completed</h3>
        <p>This valuation has been completed. The Valuation Form has been submitted and property details have been captured.</p>
        @php
            $property = \App\Models\Property::where('seller_id', $valuation->seller_id)
                ->where('address', $valuation->property_address)
                ->first();
        @endphp
        @if($property)
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main" style="margin-top: 15px;">View Property</a>
        @endif
    </div>
    @endif
</div>
@endsection

