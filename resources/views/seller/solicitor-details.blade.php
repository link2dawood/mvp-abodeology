@extends('layouts.seller')

@section('title', 'Solicitor Details')

@push('styles')
<style>
    .container {
        max-width: 700px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
    }

    .card {
        border: 1px solid #dcdcdc;
        padding: 25px;
        margin: 20px 0;
        border-radius: 4px;
        background: #fff;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        font-size: 15px;
        box-sizing: border-box;
    }

    input:focus {
        border-color: #2CB8B4;
        outline: none;
    }

    input.error {
        border-color: #dc3545;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #1E1E1E;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
    }

    .btn {
        background: #2CB8B4;
        color: #ffffff;
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-primary {
        background: #2CB8B4;
    }

    .info-box {
        background: #E8F4F3;
        border-left: 4px solid #2CB8B4;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #1E1E1E;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Solicitor Details</h2>
        <a href="{{ route('seller.properties.show', $property->id) }}" class="btn" style="background: #666;">← Back to Property</a>
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

    <div class="info-box">
        <p><strong>Property:</strong> {{ $property->address }}</p>
        <p>Please provide your solicitor's contact information. This will be used for conveyancing and legal matters related to the sale of your property.</p>
    </div>

    <form action="{{ route('seller.solicitor.details.store', $property->id) }}" method="POST">
        @csrf

        <div class="card">
            <h3>Solicitor Information</h3>

            <div>
                <label for="solicitor_name">Solicitor Name *</label>
                <input type="text" 
                       id="solicitor_name"
                       name="solicitor_name" 
                       placeholder="Enter solicitor's full name"
                       value="{{ old('solicitor_name', $property->solicitor_name) }}"
                       required
                       class="{{ $errors->has('solicitor_name') ? 'error' : '' }}">
                @error('solicitor_name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="solicitor_firm">Firm Name *</label>
                <input type="text" 
                       id="solicitor_firm"
                       name="solicitor_firm" 
                       placeholder="Enter solicitor's firm name"
                       value="{{ old('solicitor_firm', $property->solicitor_firm) }}"
                       required
                       class="{{ $errors->has('solicitor_firm') ? 'error' : '' }}">
                @error('solicitor_firm')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="solicitor_email">Email Address *</label>
                <input type="email" 
                       id="solicitor_email"
                       name="solicitor_email" 
                       placeholder="solicitor@firm.co.uk"
                       value="{{ old('solicitor_email', $property->solicitor_email) }}"
                       required
                       class="{{ $errors->has('solicitor_email') ? 'error' : '' }}">
                @error('solicitor_email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="solicitor_phone">Phone Number *</label>
                <input type="tel" 
                       id="solicitor_phone"
                       name="solicitor_phone" 
                       placeholder="020 1234 5678"
                       value="{{ old('solicitor_phone', $property->solicitor_phone) }}"
                       required
                       class="{{ $errors->has('solicitor_phone') ? 'error' : '' }}">
                @error('solicitor_phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Save Solicitor Details</button>
            <a href="{{ route('seller.properties.show', $property->id) }}" class="btn" style="background: #666;">Cancel</a>
        </div>
    </form>
</div>
@endsection
