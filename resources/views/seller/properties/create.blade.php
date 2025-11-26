@extends('layouts.seller')

@section('title', 'Create New Property')

@push('styles')
<style>
    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-top: 0;
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #1E1E1E;
    }

    label .required {
        color: #E14F4F;
    }

    input[type="text"],
    input[type="number"],
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #D9D9D9;
        border-radius: 4px;
        font-size: 15px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        box-sizing: border-box;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #2CB8B4;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .btn {
        background: #2CB8B4;
        color: #ffffff;
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: #666;
    }

    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .error-message {
        color: #E14F4F;
        font-size: 13px;
        margin-top: 5px;
    }

    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Create New Property</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 8px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.properties.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="address">
                Property Address <span class="required">*</span>
            </label>
            <textarea 
                id="address" 
                name="address" 
                rows="3"
                placeholder="Enter full property address"
                required
                class="{{ $errors->has('address') ? 'error' : '' }}"
            >{{ old('address') }}</textarea>
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="postcode">Postcode</label>
                <input 
                    type="text" 
                    id="postcode" 
                    name="postcode" 
                    placeholder="e.g., SW1A 1AA"
                    value="{{ old('postcode') }}"
                    class="{{ $errors->has('postcode') ? 'error' : '' }}"
                >
                @error('postcode')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="property_type">Property Type</label>
                <select id="property_type" name="property_type">
                    <option value="">Select property type</option>
                    <option value="detached" {{ old('property_type') == 'detached' ? 'selected' : '' }}>Detached</option>
                    <option value="semi" {{ old('property_type') == 'semi' ? 'selected' : '' }}>Semi-Detached</option>
                    <option value="terraced" {{ old('property_type') == 'terraced' ? 'selected' : '' }}>Terraced</option>
                    <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                    <option value="maisonette" {{ old('property_type') == 'maisonette' ? 'selected' : '' }}>Maisonette</option>
                    <option value="bungalow" {{ old('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                    <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('property_type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input 
                    type="number" 
                    id="bedrooms" 
                    name="bedrooms" 
                    min="0" 
                    max="50"
                    placeholder="e.g., 3"
                    value="{{ old('bedrooms') }}"
                    class="{{ $errors->has('bedrooms') ? 'error' : '' }}"
                >
                @error('bedrooms')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input 
                    type="number" 
                    id="bathrooms" 
                    name="bathrooms" 
                    min="0" 
                    max="50"
                    placeholder="e.g., 2"
                    value="{{ old('bathrooms') }}"
                    class="{{ $errors->has('bathrooms') ? 'error' : '' }}"
                >
                @error('bathrooms')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="parking">Parking</label>
                <select id="parking" name="parking">
                    <option value="">Select parking type</option>
                    <option value="none" {{ old('parking') == 'none' ? 'selected' : '' }}>None</option>
                    <option value="on_street" {{ old('parking') == 'on_street' ? 'selected' : '' }}>On-Street</option>
                    <option value="driveway" {{ old('parking') == 'driveway' ? 'selected' : '' }}>Driveway</option>
                    <option value="garage" {{ old('parking') == 'garage' ? 'selected' : '' }}>Garage</option>
                    <option value="allocated" {{ old('parking') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                    <option value="permit" {{ old('parking') == 'permit' ? 'selected' : '' }}>Permit</option>
                </select>
                @error('parking')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tenure">Tenure</label>
                <select id="tenure" name="tenure">
                    <option value="">Select tenure</option>
                    <option value="freehold" {{ old('tenure') == 'freehold' ? 'selected' : '' }}>Freehold</option>
                    <option value="leasehold" {{ old('tenure') == 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                    <option value="share_freehold" {{ old('tenure') == 'share_freehold' ? 'selected' : '' }}>Share of Freehold</option>
                    <option value="unknown" {{ old('tenure') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                </select>
                @error('tenure')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="lease_years_remaining">Lease Years Remaining</label>
                <input 
                    type="number" 
                    id="lease_years_remaining" 
                    name="lease_years_remaining" 
                    min="0"
                    placeholder="e.g., 85"
                    value="{{ old('lease_years_remaining') }}"
                    class="{{ $errors->has('lease_years_remaining') ? 'error' : '' }}"
                >
                @error('lease_years_remaining')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="help-text">Only required for leasehold properties</div>
            </div>

            <div class="form-group">
                <label for="asking_price">Asking Price (£)</label>
                <input 
                    type="number" 
                    id="asking_price" 
                    name="asking_price" 
                    min="0"
                    step="0.01"
                    placeholder="e.g., 450000"
                    value="{{ old('asking_price') }}"
                    class="{{ $errors->has('asking_price') ? 'error' : '' }}"
                >
                @error('asking_price')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="ground_rent">Ground Rent (£ per year)</label>
                <input 
                    type="number" 
                    id="ground_rent" 
                    name="ground_rent" 
                    min="0"
                    step="0.01"
                    placeholder="e.g., 250"
                    value="{{ old('ground_rent') }}"
                    class="{{ $errors->has('ground_rent') ? 'error' : '' }}"
                >
                @error('ground_rent')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="service_charge">Service Charge (£ per year)</label>
                <input 
                    type="number" 
                    id="service_charge" 
                    name="service_charge" 
                    min="0"
                    step="0.01"
                    placeholder="e.g., 1200"
                    value="{{ old('service_charge') }}"
                    class="{{ $errors->has('service_charge') ? 'error' : '' }}"
                >
                @error('service_charge')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="managing_agent">Managing Agent</label>
            <input 
                type="text" 
                id="managing_agent" 
                name="managing_agent" 
                placeholder="Name of managing agent"
                value="{{ old('managing_agent') }}"
                class="{{ $errors->has('managing_agent') ? 'error' : '' }}"
            >
            @error('managing_agent')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dcdcdc;">
            <button type="submit" class="btn">Create Property</button>
            <a href="{{ route('seller.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

