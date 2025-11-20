@extends('layouts.admin')

@section('title', 'Property Details')

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

    .status-draft { 
        background: #666; 
        color: #FFF; 
    }

    .status-property_details_completed { 
        background: #2CB8B4; 
        color: #FFF; 
    }

    .status-signed { 
        background: #28a745; 
        color: #FFF; 
    }

    .status-live { 
        background: #2CB8B4; 
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
            <h2>Property Details</h2>
        </div>
        <div>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-secondary">Back to Valuations</a>
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
        <h3>Property Information</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $property->status }}">
                    {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Address:</div>
            <div class="info-value">{{ $property->address }}</div>
        </div>
        @if($property->postcode)
        <div class="info-row">
            <div class="info-label">Postcode:</div>
            <div class="info-value">{{ $property->postcode }}</div>
        </div>
        @endif
        @if($property->property_type)
        <div class="info-row">
            <div class="info-label">Property Type:</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</div>
        </div>
        @endif
        @if($property->bedrooms)
        <div class="info-row">
            <div class="info-label">Bedrooms:</div>
            <div class="info-value">{{ $property->bedrooms }}</div>
        </div>
        @endif
        @if($property->bathrooms)
        <div class="info-row">
            <div class="info-label">Bathrooms:</div>
            <div class="info-value">{{ $property->bathrooms }}</div>
        </div>
        @endif
        @if($property->asking_price)
        <div class="info-row">
            <div class="info-label">Asking Price:</div>
            <div class="info-value">£{{ number_format($property->asking_price, 0) }}</div>
        </div>
        @endif
    </div>

    <div class="card">
        <h3>Seller Information</h3>
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $property->seller->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $property->seller->email ?? 'N/A' }}</div>
        </div>
        @if($property->seller->phone)
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $property->seller->phone }}</div>
        </div>
        @endif
    </div>

    @if($property->instruction)
    <div class="card">
        <h3>Instruction Status</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $property->instruction->status }}">
                    {{ ucfirst($property->instruction->status) }}
                </span>
            </div>
        </div>
        @if($property->instruction->signed_at)
        <div class="info-row">
            <div class="info-label">Signed Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($property->instruction->signed_at)->format('l, F j, Y g:i A') }}</div>
        </div>
        @endif
        @if($property->instruction->seller1_name)
        <div class="info-row">
            <div class="info-label">Seller 1:</div>
            <div class="info-value">{{ $property->instruction->seller1_name }}</div>
        </div>
        @endif
        @if($property->instruction->seller2_name)
        <div class="info-row">
            <div class="info-label">Seller 2:</div>
            <div class="info-value">{{ $property->instruction->seller2_name }}</div>
        </div>
        @endif
    </div>
    @endif

    @if($property->status === 'property_details_completed' && (!$property->instruction || $property->instruction->status !== 'signed'))
    <div class="card" style="background: #E8F4F3; border-left: 4px solid var(--abodeology-teal);">
        <h3 style="color: var(--abodeology-teal); margin-top: 0;">Next Steps</h3>
        <p><strong>Ask the seller if they want to instruct now or later.</strong></p>
        <p>If they choose to "Sign Up Now", you can request instruction and they will receive a link to sign the Terms & Conditions digitally.</p>
        
        @if(!$property->instruction || $property->instruction->status !== 'pending')
        <form action="{{ route('admin.properties.request-instruction', $property->id) }}" method="POST" style="margin-top: 20px;">
            @csrf
            <button type="submit" class="btn btn-main">Request Instruction from Seller</button>
            <p style="font-size: 13px; color: #666; margin-top: 10px;">
                This will send a notification to the seller with a link to sign the Terms & Conditions.
            </p>
        </form>
        @else
        <p style="color: #2CB8B4; font-weight: 600; margin-top: 15px;">
            ✓ Instruction request has been sent. Waiting for seller to sign.
        </p>
        <p style="font-size: 13px; color: #666; margin-top: 10px;">
            The seller has been notified and will receive an email with a link to sign the Terms & Conditions.
        </p>
        @endif
    </div>
    @endif

    @if($property->instruction && $property->instruction->status === 'signed')
    <div class="card" style="background: #d4edda; border-left: 4px solid #28a745;">
        <h3 style="color: #28a745; margin-top: 0;">✓ Instruction Signed</h3>
        <p>Congratulations! The seller has signed the Terms & Conditions. The Welcome Pack has been sent to the seller.</p>
        <p><strong>Signed Date:</strong> {{ \Carbon\Carbon::parse($property->instruction->signed_at)->format('l, F j, Y g:i A') }}</p>
    </div>
    @endif
</div>
@endsection

