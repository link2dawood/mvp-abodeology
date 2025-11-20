@extends('layouts.seller')

@section('title', 'Seller Onboarding')

@push('styles')
<style>
    /* MAIN */
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

    .section-title {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 15px;
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
    }

    /* FORM FIELDS */
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
    }

    input.error,
    select.error,
    textarea.error {
        border-color: #dc3545;
    }

    /* ERROR MESSAGES */
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
        text-align: left;
    }

    /* SUCCESS MESSAGE */
    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 20px;
        color: #155724;
        font-size: 14px;
        text-align: left;
    }

    /* GRID */
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    /* UPLOAD BOX */
    .upload-box {
        background: var(--soft-grey);
        border: 1px dashed #CCCCCC;
        padding: 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .upload-box strong {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .upload-box input[type="file"] {
        margin-top: 10px;
        width: auto;
        padding: 8px;
    }

    /* BUTTON */
    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 14px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        border: none;
        cursor: pointer;
        font-size: 15px;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn:active {
        transform: scale(0.98);
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Seller Onboarding</h2>
    <p class="page-subtitle">Provide your material information so we can legally prepare your listing.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px; text-align: left;">
            <strong>Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.onboarding.store', $propertyId ?? 1) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- SECTION 1: PROPERTY DETAILS -->
        <div class="card">
            <h3>Property Details</h3>
            <input type="text" 
                   name="property_address" 
                   placeholder="Full property address" 
                   value="{{ old('property_address', $onboarding->property_address ?? '') }}"
                   required
                   class="{{ $errors->has('property_address') ? 'error' : '' }}">
            @error('property_address')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="property_type" 
                    required
                    class="{{ $errors->has('property_type') ? 'error' : '' }}">
                <option value="">Property type</option>
                <option value="detached" {{ old('property_type', $onboarding->property_type ?? '') == 'detached' ? 'selected' : '' }}>Detached</option>
                <option value="semi-detached" {{ old('property_type', $onboarding->property_type ?? '') == 'semi-detached' ? 'selected' : '' }}>Semi-detached</option>
                <option value="terraced" {{ old('property_type', $onboarding->property_type ?? '') == 'terraced' ? 'selected' : '' }}>Terraced</option>
                <option value="flat-maisonette" {{ old('property_type', $onboarding->property_type ?? '') == 'flat-maisonette' ? 'selected' : '' }}>Flat/Maisonette</option>
                <option value="bungalow" {{ old('property_type', $onboarding->property_type ?? '') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                <option value="other" {{ old('property_type', $onboarding->property_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('property_type')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="grid-2">
                <input type="number" 
                       name="bedrooms" 
                       placeholder="Number of bedrooms" 
                       value="{{ old('bedrooms', $onboarding->bedrooms ?? '') }}"
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
            </div>

            <select name="parking" 
                    class="{{ $errors->has('parking') ? 'error' : '' }}">
                <option value="">Parking</option>
                <option value="none" {{ old('parking', $onboarding->parking ?? '') == 'none' ? 'selected' : '' }}>None</option>
                <option value="on-street" {{ old('parking', $onboarding->parking ?? '') == 'on-street' ? 'selected' : '' }}>On-street</option>
                <option value="driveway" {{ old('parking', $onboarding->parking ?? '') == 'driveway' ? 'selected' : '' }}>Driveway</option>
                <option value="garage" {{ old('parking', $onboarding->parking ?? '') == 'garage' ? 'selected' : '' }}>Garage</option>
                <option value="allocated" {{ old('parking', $onboarding->parking ?? '') == 'allocated' ? 'selected' : '' }}>Allocated parking</option>
                <option value="permit" {{ old('parking', $onboarding->parking ?? '') == 'permit' ? 'selected' : '' }}>Permit required</option>
            </select>
            @error('parking')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SECTION 2: TENURE -->
        <div class="card">
            <h3>Tenure</h3>
            <select name="tenure" 
                    required
                    class="{{ $errors->has('tenure') ? 'error' : '' }}">
                <option value="">Select tenure</option>
                <option value="freehold" {{ old('tenure', $onboarding->tenure ?? '') == 'freehold' ? 'selected' : '' }}>Freehold</option>
                <option value="leasehold" {{ old('tenure', $onboarding->tenure ?? '') == 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                <option value="share-of-freehold" {{ old('tenure', $onboarding->tenure ?? '') == 'share-of-freehold' ? 'selected' : '' }}>Share of Freehold</option>
                <option value="unknown" {{ old('tenure', $onboarding->tenure ?? '') == 'unknown' ? 'selected' : '' }}>Unknown</option>
            </select>
            @error('tenure')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="grid-2">
                <input type="number" 
                       name="lease_years" 
                       placeholder="Lease years remaining" 
                       value="{{ old('lease_years', $onboarding->lease_years ?? '') }}"
                       min="0"
                       class="{{ $errors->has('lease_years') ? 'error' : '' }}">
                @error('lease_years')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="text" 
                       name="ground_rent" 
                       placeholder="Ground rent (£)" 
                       value="{{ old('ground_rent', $onboarding->ground_rent ?? '') }}"
                       class="{{ $errors->has('ground_rent') ? 'error' : '' }}">
                @error('ground_rent')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="text" 
                       name="service_charge" 
                       placeholder="Service charge (£)" 
                       value="{{ old('service_charge', $onboarding->service_charge ?? '') }}"
                       class="{{ $errors->has('service_charge') ? 'error' : '' }}">
                @error('service_charge')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="text" 
                       name="managing_agent" 
                       placeholder="Managing agent (optional)" 
                       value="{{ old('managing_agent', $onboarding->managing_agent ?? '') }}"
                       class="{{ $errors->has('managing_agent') ? 'error' : '' }}">
                @error('managing_agent')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- SECTION 3: OWNERSHIP -->
        <div class="card">
            <h3>Ownership & Legal</h3>
            <select name="legal_owner" 
                    class="{{ $errors->has('legal_owner') ? 'error' : '' }}">
                <option value="">Are you the legal owner?</option>
                <option value="yes" {{ old('legal_owner', $onboarding->legal_owner ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('legal_owner', $onboarding->legal_owner ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('legal_owner')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="mortgaged" 
                    class="{{ $errors->has('mortgaged') ? 'error' : '' }}">
                <option value="">Is the property mortgaged?</option>
                <option value="yes" {{ old('mortgaged', $onboarding->mortgaged ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('mortgaged', $onboarding->mortgaged ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('mortgaged')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="mortgage_lender" 
                   placeholder="Mortgage lender (if applicable)" 
                   value="{{ old('mortgage_lender', $onboarding->mortgage_lender ?? '') }}"
                   class="{{ $errors->has('mortgage_lender') ? 'error' : '' }}">
            @error('mortgage_lender')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="notices_charges" 
                      placeholder="Any notices or charges registered on the property?"
                      class="{{ $errors->has('notices_charges') ? 'error' : '' }}">{{ old('notices_charges', $onboarding->notices_charges ?? '') }}</textarea>
            @error('notices_charges')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SECTION 4: UTILITIES -->
        <div class="card">
            <h3>Utilities & Services</h3>
            <div class="grid-2">
                <select name="gas_supply" 
                        class="{{ $errors->has('gas_supply') ? 'error' : '' }}">
                    <option value="">Gas supply?</option>
                    <option value="yes" {{ old('gas_supply', $onboarding->gas_supply ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('gas_supply', $onboarding->gas_supply ?? '') == 'no' ? 'selected' : '' }}>No</option>
                </select>
                @error('gas_supply')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="electricity_supply" 
                        class="{{ $errors->has('electricity_supply') ? 'error' : '' }}">
                    <option value="">Electricity supply?</option>
                    <option value="yes" {{ old('electricity_supply', $onboarding->electricity_supply ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('electricity_supply', $onboarding->electricity_supply ?? '') == 'no' ? 'selected' : '' }}>No</option>
                </select>
                @error('electricity_supply')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="mains_water" 
                        class="{{ $errors->has('mains_water') ? 'error' : '' }}">
                    <option value="">Mains water?</option>
                    <option value="yes" {{ old('mains_water', $onboarding->mains_water ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('mains_water', $onboarding->mains_water ?? '') == 'no' ? 'selected' : '' }}>No</option>
                </select>
                @error('mains_water')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <select name="drainage" 
                        class="{{ $errors->has('drainage') ? 'error' : '' }}">
                    <option value="">Drainage</option>
                    <option value="mains" {{ old('drainage', $onboarding->drainage ?? '') == 'mains' ? 'selected' : '' }}>Mains</option>
                    <option value="septic-tank" {{ old('drainage', $onboarding->drainage ?? '') == 'septic-tank' ? 'selected' : '' }}>Septic tank</option>
                    <option value="private" {{ old('drainage', $onboarding->drainage ?? '') == 'private' ? 'selected' : '' }}>Private system</option>
                </select>
                @error('drainage')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <input type="number" 
                   name="boiler_age" 
                   placeholder="Boiler age (years)" 
                   value="{{ old('boiler_age', $onboarding->boiler_age ?? '') }}"
                   min="0"
                   class="{{ $errors->has('boiler_age') ? 'error' : '' }}">
            @error('boiler_age')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="date" 
                   name="last_boiler_service" 
                   placeholder="Last boiler service (if known)" 
                   value="{{ old('last_boiler_service', $onboarding->last_boiler_service ?? '') }}"
                   class="{{ $errors->has('last_boiler_service') ? 'error' : '' }}">
            @error('last_boiler_service')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="epc_rating" 
                   placeholder="EPC rating (A–G)" 
                   value="{{ old('epc_rating', $onboarding->epc_rating ?? '') }}"
                   maxlength="1"
                   pattern="[A-G]"
                   class="{{ $errors->has('epc_rating') ? 'error' : '' }}">
            @error('epc_rating')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SECTION 5: MATERIAL INFORMATION -->
        <div class="card">
            <h3>Material Information (Required by Trading Standards)</h3>
            <textarea name="known_issues" 
                      placeholder="Any known issues? (damp, subsidence, Japanese knotweed, neighbours, disputes)"
                      class="{{ $errors->has('known_issues') ? 'error' : '' }}">{{ old('known_issues', $onboarding->known_issues ?? '') }}</textarea>
            @error('known_issues')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="alterations" 
                      placeholder="Have there been any alterations requiring planning permission or building control?"
                      class="{{ $errors->has('alterations') ? 'error' : '' }}">{{ old('alterations', $onboarding->alterations ?? '') }}</textarea>
            @error('alterations')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="upload-box">
                <strong>Upload certificates (planning, building regs, FENSA etc.)</strong>
                <input type="file" 
                       name="certificates[]" 
                       multiple 
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="{{ $errors->has('certificates') ? 'error' : '' }}">
            </div>
            @error('certificates')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SECTION 6: ACCESS -->
        <div class="card">
            <h3>Access & Viewings</h3>
            <select name="viewing_contact" 
                    class="{{ $errors->has('viewing_contact') ? 'error' : '' }}">
                <option value="">Who should we contact to arrange viewings?</option>
                <option value="seller1" {{ old('viewing_contact', $onboarding->viewing_contact ?? '') == 'seller1' ? 'selected' : '' }}>Seller 1</option>
                <option value="seller2" {{ old('viewing_contact', $onboarding->viewing_contact ?? '') == 'seller2' ? 'selected' : '' }}>Seller 2</option>
                <option value="tenant" {{ old('viewing_contact', $onboarding->viewing_contact ?? '') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                <option value="other" {{ old('viewing_contact', $onboarding->viewing_contact ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
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
                      placeholder="Alarm codes / access notes (optional)"
                      class="{{ $errors->has('access_notes') ? 'error' : '' }}">{{ old('access_notes', $onboarding->access_notes ?? '') }}</textarea>
            @error('access_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SECTION 7: MARKETING PERMISSIONS -->
        <div class="card">
            <h3>Marketing Permissions</h3>
            <select name="for_sale_board" 
                    class="{{ $errors->has('for_sale_board') ? 'error' : '' }}">
                <option value="">Erect a "For Sale" board?</option>
                <option value="yes" {{ old('for_sale_board', $onboarding->for_sale_board ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('for_sale_board', $onboarding->for_sale_board ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('for_sale_board')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="photography_homecheck" 
                    class="{{ $errors->has('photography_homecheck') ? 'error' : '' }}">
                <option value="">Authorise photography, 360 images & HomeCheck?</option>
                <option value="yes" {{ old('photography_homecheck', $onboarding->photography_homecheck ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('photography_homecheck', $onboarding->photography_homecheck ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('photography_homecheck')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="publish_marketing" 
                    class="{{ $errors->has('publish_marketing') ? 'error' : '' }}">
                <option value="">Authorise us to publish your marketing materials?</option>
                <option value="yes" {{ old('publish_marketing', $onboarding->publish_marketing ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('publish_marketing', $onboarding->publish_marketing ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('publish_marketing')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SUBMIT -->
        <button type="submit" class="btn">Save & Continue</button>
    </form>
</div>
@endsection
