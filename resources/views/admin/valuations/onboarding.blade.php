@extends('layouts.admin')

@section('title', 'Complete Seller Onboarding - Valuation #' . $valuation->id)

@push('styles')
<style>
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .valuation-info {
        background: #E8F4F3;
        border-left: 4px solid var(--abodeology-teal);
        padding: 15px 20px;
        margin-bottom: 25px;
        border-radius: 4px;
    }

    .valuation-info strong {
        color: var(--abodeology-teal);
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        margin-bottom: 30px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: 600;
        color: var(--abodeology-teal);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 14px;
        margin-bottom: 18px;
        border: 1px solid #D9D9D9;
        border-radius: 6px;
        outline: none;
        font-size: 15px;
        box-sizing: border-box;
    }

    textarea {
        height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    input.error,
    select.error,
    textarea.error {
        border-color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
        text-align: left;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 14px 30px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        margin-right: 10px;
        border: none;
        cursor: pointer;
        font-size: 15px;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: #6c757d;
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
            <h2>Complete Seller Onboarding</h2>
            <p class="page-subtitle">Fill in the seller onboarding form after attending the valuation appointment.</p>
        </div>
        <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-secondary">Back to Valuation</a>
    </div>

    <div class="valuation-info">
        <strong>Valuation Request:</strong> {{ $valuation->property_address }}<br>
        <strong>Client:</strong> {{ $valuation->seller->name }} ({{ $valuation->seller->email }})<br>
        @if($valuation->valuation_date)
            <strong>Valuation Date:</strong> {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}
            @if($valuation->valuation_time)
                at {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}
            @endif
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px; text-align: left;">
            <strong>Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.valuations.onboarding.store', $valuation->id) }}" method="POST">
        @csrf

        <!-- PROPERTY DETAILS -->
        <div class="card">
            <h3>Property Details</h3>
            <input type="text" 
                   name="property_address" 
                   placeholder="Full property address" 
                   value="{{ old('property_address', $onboarding->property_address ?? $valuation->property_address) }}"
                   required
                   class="{{ $errors->has('property_address') ? 'error' : '' }}">
            @error('property_address')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="grid-2">
                <input type="text" 
                       name="postcode" 
                       placeholder="Postcode" 
                       value="{{ old('postcode', $onboarding->postcode ?? $valuation->postcode) }}"
                       class="{{ $errors->has('postcode') ? 'error' : '' }}">
                @error('postcode')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="property_type" 
                        required
                        class="{{ $errors->has('property_type') ? 'error' : '' }}">
                    <option value="">Property type</option>
                    <option value="detached" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'detached' ? 'selected' : '' }}>Detached</option>
                    <option value="semi" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'semi' ? 'selected' : '' }}>Semi-Detached</option>
                    <option value="terraced" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'terraced' ? 'selected' : '' }}>Terraced</option>
                    <option value="flat" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'flat' ? 'selected' : '' }}>Flat</option>
                    <option value="maisonette" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'maisonette' ? 'selected' : '' }}>Maisonette</option>
                    <option value="bungalow" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                    <option value="other" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('property_type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid-2">
                <input type="number" 
                       name="bedrooms" 
                       placeholder="Number of bedrooms" 
                       value="{{ old('bedrooms', $onboarding->bedrooms ?? $valuation->bedrooms) }}"
                       min="0"
                       required
                       class="{{ $errors->has('bedrooms') ? 'error' : '' }}">
                @error('bedrooms')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="number" 
                       name="bathrooms" 
                       placeholder="Number of bathrooms" 
                       value="{{ old('bathrooms', $onboarding->bathrooms ?? '') }}"
                       min="0"
                       step="0.5"
                       required
                       class="{{ $errors->has('bathrooms') ? 'error' : '' }}">
                @error('bathrooms')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="parking" 
                        class="{{ $errors->has('parking') ? 'error' : '' }}">
                    <option value="">Parking</option>
                    <option value="none" {{ old('parking', $onboarding->parking ?? '') == 'none' ? 'selected' : '' }}>None</option>
                    <option value="on_street" {{ old('parking', $onboarding->parking ?? '') == 'on_street' ? 'selected' : '' }}>On-street</option>
                    <option value="driveway" {{ old('parking', $onboarding->parking ?? '') == 'driveway' ? 'selected' : '' }}>Driveway</option>
                    <option value="garage" {{ old('parking', $onboarding->parking ?? '') == 'garage' ? 'selected' : '' }}>Garage</option>
                    <option value="allocated" {{ old('parking', $onboarding->parking ?? '') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                    <option value="permit" {{ old('parking', $onboarding->parking ?? '') == 'permit' ? 'selected' : '' }}>Permit</option>
                </select>
                @error('parking')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="number" 
                       name="asking_price" 
                       placeholder="Asking Price (£)" 
                       value="{{ old('asking_price', $onboarding->asking_price ?? '') }}"
                       min="0"
                       step="0.01"
                       class="{{ $errors->has('asking_price') ? 'error' : '' }}">
                @error('asking_price')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- TENURE -->
        <div class="card">
            <h3>Tenure</h3>
            <select name="tenure" 
                    required
                    class="{{ $errors->has('tenure') ? 'error' : '' }}">
                <option value="">Select tenure</option>
                <option value="freehold" {{ old('tenure', $onboarding->tenure ?? '') == 'freehold' ? 'selected' : '' }}>Freehold</option>
                <option value="leasehold" {{ old('tenure', $onboarding->tenure ?? '') == 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                <option value="share_freehold" {{ old('tenure', $onboarding->tenure ?? '') == 'share_freehold' ? 'selected' : '' }}>Share of Freehold</option>
                <option value="unknown" {{ old('tenure', $onboarding->tenure ?? '') == 'unknown' ? 'selected' : '' }}>Unknown</option>
            </select>
            @error('tenure')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="grid-2">
                <input type="number" 
                       name="lease_years_remaining" 
                       placeholder="Lease years remaining" 
                       value="{{ old('lease_years_remaining', $onboarding->lease_years_remaining ?? '') }}"
                       min="0"
                       class="{{ $errors->has('lease_years_remaining') ? 'error' : '' }}">
                @error('lease_years_remaining')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="number" 
                       name="ground_rent" 
                       placeholder="Ground rent (£)" 
                       value="{{ old('ground_rent', $onboarding->ground_rent ?? '') }}"
                       min="0"
                       step="0.01"
                       class="{{ $errors->has('ground_rent') ? 'error' : '' }}">
                @error('ground_rent')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="number" 
                       name="service_charge" 
                       placeholder="Service charge (£)" 
                       value="{{ old('service_charge', $onboarding->service_charge ?? '') }}"
                       min="0"
                       step="0.01"
                       class="{{ $errors->has('service_charge') ? 'error' : '' }}">
                @error('service_charge')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="text" 
                       name="managing_agent" 
                       placeholder="Managing agent" 
                       value="{{ old('managing_agent', $onboarding->managing_agent ?? '') }}"
                       class="{{ $errors->has('managing_agent') ? 'error' : '' }}">
                @error('managing_agent')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- MATERIAL INFORMATION -->
        <div class="card">
            <h3>Material Information</h3>
            <select name="heating_type" 
                    class="{{ $errors->has('heating_type') ? 'error' : '' }}">
                <option value="">Heating Type</option>
                <option value="gas" {{ old('heating_type', $onboarding->heating_type ?? '') == 'gas' ? 'selected' : '' }}>Gas</option>
                <option value="electric" {{ old('heating_type', $onboarding->heating_type ?? '') == 'electric' ? 'selected' : '' }}>Electric</option>
                <option value="oil" {{ old('heating_type', $onboarding->heating_type ?? '') == 'oil' ? 'selected' : '' }}>Oil</option>
                <option value="underfloor" {{ old('heating_type', $onboarding->heating_type ?? '') == 'underfloor' ? 'selected' : '' }}>Underfloor</option>
                <option value="other" {{ old('heating_type', $onboarding->heating_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('heating_type')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="grid-2">
                <input type="number" 
                       name="boiler_age_years" 
                       placeholder="Boiler age (years)" 
                       value="{{ old('boiler_age_years', $onboarding->boiler_age_years ?? '') }}"
                       min="0"
                       class="{{ $errors->has('boiler_age_years') ? 'error' : '' }}">
                @error('boiler_age_years')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="date" 
                       name="boiler_last_serviced" 
                       placeholder="Last boiler service" 
                       value="{{ old('boiler_last_serviced', $onboarding->boiler_last_serviced ?? '') }}"
                       class="{{ $errors->has('boiler_last_serviced') ? 'error' : '' }}">
                @error('boiler_last_serviced')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="epc_rating" 
                        class="{{ $errors->has('epc_rating') ? 'error' : '' }}">
                    <option value="">EPC Rating</option>
                    <option value="A" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'C' ? 'selected' : '' }}>C</option>
                    <option value="D" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'D' ? 'selected' : '' }}>D</option>
                    <option value="E" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'E' ? 'selected' : '' }}>E</option>
                    <option value="F" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'F' ? 'selected' : '' }}>F</option>
                    <option value="G" {{ old('epc_rating', $onboarding->epc_rating ?? '') == 'G' ? 'selected' : '' }}>G</option>
                </select>
                @error('epc_rating')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid-2">
                <label style="display: flex; align-items: center; margin-bottom: 10px;">
                    <input type="checkbox" 
                           name="gas_supply" 
                           value="1" 
                           {{ old('gas_supply', $onboarding->gas_supply ?? false) ? 'checked' : '' }}
                           style="width: auto; margin-right: 8px; margin-bottom: 0;">
                    Gas Supply
                </label>
                <label style="display: flex; align-items: center; margin-bottom: 10px;">
                    <input type="checkbox" 
                           name="electricity_supply" 
                           value="1" 
                           {{ old('electricity_supply', $onboarding->electricity_supply ?? false) ? 'checked' : '' }}
                           style="width: auto; margin-right: 8px; margin-bottom: 0;">
                    Electricity Supply
                </label>
                <label style="display: flex; align-items: center; margin-bottom: 10px;">
                    <input type="checkbox" 
                           name="mains_water" 
                           value="1" 
                           {{ old('mains_water', $onboarding->mains_water ?? false) ? 'checked' : '' }}
                           style="width: auto; margin-right: 8px; margin-bottom: 0;">
                    Mains Water
                </label>
                <select name="drainage" 
                        class="{{ $errors->has('drainage') ? 'error' : '' }}">
                    <option value="">Drainage</option>
                    <option value="mains" {{ old('drainage', $onboarding->drainage ?? '') == 'mains' ? 'selected' : '' }}>Mains</option>
                    <option value="septic_tank" {{ old('drainage', $onboarding->drainage ?? '') == 'septic_tank' ? 'selected' : '' }}>Septic Tank</option>
                    <option value="private_system" {{ old('drainage', $onboarding->drainage ?? '') == 'private_system' ? 'selected' : '' }}>Private System</option>
                </select>
                @error('drainage')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <textarea name="known_issues" 
                      placeholder="Known issues (damp, subsidence, Japanese knotweed, neighbours, disputes, etc.)"
                      class="{{ $errors->has('known_issues') ? 'error' : '' }}">{{ old('known_issues', $onboarding->known_issues ?? '') }}</textarea>
            @error('known_issues')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="planning_alterations" 
                      placeholder="Planning alterations or building control changes"
                      class="{{ $errors->has('planning_alterations') ? 'error' : '' }}">{{ old('planning_alterations', $onboarding->planning_alterations ?? '') }}</textarea>
            @error('planning_alterations')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- ACCESS & NOTES -->
        <div class="card">
            <h3>Access & Viewing Information</h3>
            <input type="text" 
                   name="viewing_contact" 
                   placeholder="Viewing contact person" 
                   value="{{ old('viewing_contact', $onboarding->viewing_contact ?? '') }}"
                   class="{{ $errors->has('viewing_contact') ? 'error' : '' }}">
            @error('viewing_contact')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="preferred_viewing_times" 
                      placeholder="Preferred viewing times"
                      class="{{ $errors->has('preferred_viewing_times') ? 'error' : '' }}">{{ old('preferred_viewing_times', $onboarding->preferred_viewing_times ?? '') }}</textarea>
            @error('preferred_viewing_times')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="access_notes" 
                      placeholder="Access notes (alarm codes, keys location, access instructions, etc.)"
                      class="{{ $errors->has('access_notes') ? 'error' : '' }}">{{ old('access_notes', $onboarding->access_notes ?? '') }}</textarea>
            @error('access_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- VALUATION & AGENT NOTES -->
        <div class="card">
            <h3>Valuation & Notes</h3>
            <input type="number" 
                   name="estimated_value" 
                   placeholder="Estimated Value (£)" 
                   value="{{ old('estimated_value', $valuation->estimated_value ?? '') }}"
                   min="0"
                   step="0.01"
                   class="{{ $errors->has('estimated_value') ? 'error' : '' }}">
            @error('estimated_value')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="agent_notes" 
                      placeholder="Agent notes (valuation observations, condition notes, recommendations, etc.)"
                      class="{{ $errors->has('agent_notes') ? 'error' : '' }}">{{ old('agent_notes', $valuation->notes ?? '') }}</textarea>
            @error('agent_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SUBMIT -->
        <div style="margin-top: 30px;">
            <button type="submit" class="btn">Complete Onboarding & Create Property</button>
            <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

