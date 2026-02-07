@extends('layouts.admin')

@section('title', 'Valuation Form - Valuation #' . $valuation->id)

@push('styles')
<style>
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 24px 40px;
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
        padding: 18px 24px;
        margin-bottom: 24px;
        border-radius: 8px;
    }

    .valuation-info strong {
        color: var(--abodeology-teal);
    }

    .card {
        background: var(--white);
        padding: 24px 28px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        margin-bottom: 24px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card:last-of-type {
        margin-bottom: 0;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 18px;
        padding-bottom: 12px;
        font-size: 20px;
        font-weight: 600;
        color: var(--abodeology-teal);
        border-bottom: 1px solid var(--line-grey);
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-row .form-group {
        margin-bottom: 18px;
    }

    .form-row .form-group:last-child {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-group label.required::after {
        content: " *";
        color: #dc3545;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group input[type="number"],
    .form-group input[type="date"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-group .help-text {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-right: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
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

    .pre-filled-note {
        background: #d4edda;
        padding: 12px 16px;
        margin-bottom: 18px;
        border-radius: 6px;
        font-size: 13px;
        color: #155724;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Valuation Form</h2>
            <p class="page-subtitle">Complete this form on-site during the valuation appointment. Information is pre-filled with seller and property details.</p>
        </div>
        <div>
            <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-secondary">Back to Valuation</a>
        </div>
    </div>

    <div class="valuation-info">
        <p><strong>Valuation Appointment:</strong> 
            @if($valuation->valuation_date)
                {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}
                @if($valuation->valuation_time)
                    at {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}
                @endif
            @else
                Not scheduled
            @endif
        </p>
        <p><strong>Property:</strong> {{ $valuation->property_address }}</p>
        <p><strong>Seller:</strong> {{ $valuation->seller->name ?? 'N/A' }}</p>
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

    <form action="{{ route('admin.valuations.valuation-form.store', $valuation->id) }}" method="POST">
        @csrf

        <!-- Pre-filled Seller Information Section -->
        <div class="card">
            <h3>Seller Information <span style="font-size: 14px; font-weight: normal; color: #666;">(Pre-filled from Valuation)</span></h3>
            <div class="pre-filled-note">
                ✓ The following information has been pre-filled from the valuation booking. You can update it if needed.
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="seller_name" value="{{ $onboarding->seller_name ?? $valuation->seller->name ?? '' }}" readonly style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="seller_email" value="{{ $onboarding->seller_email ?? $valuation->seller->email ?? '' }}" readonly style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="seller_phone" value="{{ old('seller_phone', $onboarding->seller_phone ?? $valuation->seller->phone ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Property Details Section -->
        <div class="card">
            <h3>Property Details</h3>
            <div class="pre-filled-note">
                ✓ Property address and basic details are pre-filled from the valuation booking.
            </div>
            <div class="form-group">
                <label class="required">Property Address</label>
                <input type="text" 
                       id="property_address" 
                       name="property_address" 
                       placeholder="Start typing your address..."
                       value="{{ old('property_address', $onboarding->property_address ?? $valuation->property_address ?? '') }}" 
                       required
                       autocomplete="address-line1">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Postcode</label>
                    <input type="text" 
                           id="postcode" 
                           name="postcode" 
                           placeholder="Postcode"
                           value="{{ old('postcode', $onboarding->postcode ?? $valuation->postcode ?? '') }}"
                           autocomplete="postal-code">
                </div>
                <div class="form-group">
                    <label class="required">Property Type</label>
                    <select name="property_type" required>
                        <option value="">Select...</option>
                        <option value="detached" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'detached' ? 'selected' : '' }}>Detached</option>
                        <option value="semi" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'semi' ? 'selected' : '' }}>Semi-Detached</option>
                        <option value="terraced" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'terraced' ? 'selected' : '' }}>Terraced</option>
                        <option value="flat" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="maisonette" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'maisonette' ? 'selected' : '' }}>Maisonette</option>
                        <option value="bungalow" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                        <option value="other" {{ old('property_type', $onboarding->property_type ?? $valuation->property_type) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="required">Bedrooms</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $onboarding->bedrooms ?? $valuation->bedrooms ?? '') }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="required">Bathrooms</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $onboarding->bathrooms ?? '') }}" step="0.5" min="0" required>
                </div>
                <div class="form-group">
                    <label>Reception Rooms</label>
                    <input type="number" name="reception_rooms" value="{{ old('reception_rooms', $onboarding->reception_rooms ?? '') }}" min="0">
                </div>
            </div>
            <div class="form-group">
                <label>Outbuildings</label>
                <input type="text" name="outbuildings" value="{{ old('outbuildings', $onboarding->outbuildings ?? '') }}" placeholder="e.g., garage, shed, workshop">
                <div class="help-text">List any outbuildings on the property</div>
            </div>
            <div class="form-group">
                <label>Garden Details</label>
                <textarea name="garden_details" rows="3" placeholder="Garden size, type, features, etc.">{{ old('garden_details', $onboarding->garden_details ?? '') }}</textarea>
                <div class="help-text">Describe the garden, including size, type, and any notable features</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Parking</label>
                    <select name="parking">
                        <option value="">Select...</option>
                        <option value="none" {{ old('parking', $onboarding->parking ?? '') === 'none' ? 'selected' : '' }}>None</option>
                        <option value="on_street" {{ old('parking', $onboarding->parking ?? '') === 'on_street' ? 'selected' : '' }}>On Street</option>
                        <option value="driveway" {{ old('parking', $onboarding->parking ?? '') === 'driveway' ? 'selected' : '' }}>Driveway</option>
                        <option value="garage" {{ old('parking', $onboarding->parking ?? '') === 'garage' ? 'selected' : '' }}>Garage</option>
                        <option value="allocated" {{ old('parking', $onboarding->parking ?? '') === 'allocated' ? 'selected' : '' }}>Allocated</option>
                        <option value="permit" {{ old('parking', $onboarding->parking ?? '') === 'permit' ? 'selected' : '' }}>Permit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="required">Tenure</label>
                    <select name="tenure" required>
                        <option value="">Select...</option>
                        <option value="freehold" {{ old('tenure', $onboarding->tenure ?? '') === 'freehold' ? 'selected' : '' }}>Freehold</option>
                        <option value="leasehold" {{ old('tenure', $onboarding->tenure ?? '') === 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                        <option value="share_freehold" {{ old('tenure', $onboarding->tenure ?? '') === 'share_freehold' ? 'selected' : '' }}>Share of Freehold</option>
                        <option value="unknown" {{ old('tenure', $onboarding->tenure ?? '') === 'unknown' ? 'selected' : '' }}>Unknown</option>
                    </select>
                </div>
            </div>
            @if($onboarding->tenure === 'leasehold' || !$onboarding->tenure || old('tenure') == 'leasehold')
            <div class="form-row" id="leasehold-details">
                <div class="form-group">
                    <label>Lease Years Remaining</label>
                    <input type="number" name="lease_years_remaining" value="{{ old('lease_years_remaining', $onboarding->lease_years_remaining ?? '') }}" min="0">
                </div>
                <div class="form-group">
                    <label>Ground Rent (£/year)</label>
                    <input type="number" name="ground_rent" value="{{ old('ground_rent', $onboarding->ground_rent ?? '') }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Service Charge (£/year)</label>
                    <input type="number" name="service_charge" value="{{ old('service_charge', $onboarding->service_charge ?? '') }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Managing Agent</label>
                    <input type="text" name="managing_agent" value="{{ old('managing_agent', $onboarding->managing_agent ?? '') }}">
                </div>
            </div>
            @endif
            <div class="form-row">
                <div class="form-group">
                    <label>Asking Price (£)</label>
                    <input type="number" name="asking_price" value="{{ old('asking_price', $onboarding->asking_price ?? '') }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Estimated Value (£)</label>
                    <input type="number" name="estimated_value" value="{{ old('estimated_value', $onboarding->estimated_value ?? $valuation->estimated_value ?? '') }}" step="0.01" min="0">
                </div>
            </div>
        </div>

        <!-- MATERIAL INFORMATION -->
        <div class="card">
            <h3>Material Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Heating Type</label>
                    <select name="heating_type">
                        <option value="">Select...</option>
                        <option value="gas" {{ old('heating_type', $onboarding->heating_type ?? '') === 'gas' ? 'selected' : '' }}>Gas</option>
                        <option value="electric" {{ old('heating_type', $onboarding->heating_type ?? '') === 'electric' ? 'selected' : '' }}>Electric</option>
                        <option value="oil" {{ old('heating_type', $onboarding->heating_type ?? '') === 'oil' ? 'selected' : '' }}>Oil</option>
                        <option value="underfloor" {{ old('heating_type', $onboarding->heating_type ?? '') === 'underfloor' ? 'selected' : '' }}>Underfloor</option>
                        <option value="other" {{ old('heating_type', $onboarding->heating_type ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Boiler Age (years)</label>
                    <input type="number" name="boiler_age_years" value="{{ old('boiler_age_years', $onboarding->boiler_age_years ?? '') }}" min="0">
                </div>
                <div class="form-group">
                    <label>Last Boiler Service</label>
                    <input type="date" name="boiler_last_serviced" value="{{ old('boiler_last_serviced', $onboarding->boiler_last_serviced ?? '') }}">
                </div>
                <div class="form-group">
                    <label>EPC Rating</label>
                    <select name="epc_rating">
                        <option value="">Select...</option>
                        <option value="awaiting" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'awaiting' ? 'selected' : '' }}>Awaiting</option>
                        <option value="A" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'C' ? 'selected' : '' }}>C</option>
                        <option value="D" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'D' ? 'selected' : '' }}>D</option>
                        <option value="E" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'E' ? 'selected' : '' }}>E</option>
                        <option value="F" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'F' ? 'selected' : '' }}>F</option>
                        <option value="G" {{ old('epc_rating', $onboarding->epc_rating ?? '') === 'G' ? 'selected' : '' }}>G</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="gas_supply" value="1" {{ old('gas_supply', $onboarding->gas_supply ?? false) ? 'checked' : '' }} style="width: auto; margin-right: 8px;">
                        Gas Supply
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="electricity_supply" value="1" {{ old('electricity_supply', $onboarding->electricity_supply ?? false) ? 'checked' : '' }} style="width: auto; margin-right: 8px;">
                        Electricity Supply
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="mains_water" value="1" {{ old('mains_water', $onboarding->mains_water ?? false) ? 'checked' : '' }} style="width: auto; margin-right: 8px;">
                        Mains Water
                    </label>
                </div>
                <div class="form-group">
                    <label>Drainage</label>
                    <select name="drainage">
                        <option value="">Select...</option>
                        <option value="mains" {{ old('drainage', $onboarding->drainage ?? '') === 'mains' ? 'selected' : '' }}>Mains</option>
                        <option value="septic_tank" {{ old('drainage', $onboarding->drainage ?? '') === 'septic_tank' ? 'selected' : '' }}>Septic Tank</option>
                        <option value="private_system" {{ old('drainage', $onboarding->drainage ?? '') === 'private_system' ? 'selected' : '' }}>Private System</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Known Issues</label>
                <textarea name="known_issues" rows="4" placeholder="Damp, subsidence, Japanese knotweed, neighbours, disputes, etc.">{{ old('known_issues', $onboarding->known_issues ?? '') }}</textarea>
                <div class="help-text">List any known issues or problems with the property</div>
            </div>
            <div class="form-group">
                <label>Planning Alterations</label>
                <textarea name="planning_alterations" rows="4" placeholder="Planning alterations or building control changes">{{ old('planning_alterations', $onboarding->planning_alterations ?? '') }}</textarea>
                <div class="help-text">Note any planning alterations or building control changes</div>
            </div>
        </div>

        <!-- ACCESS & NOTES -->
        <div class="card">
            <h3>Access & Viewing Information</h3>
            <div class="form-group">
                <label>Viewing Contact</label>
                <input type="text" name="viewing_contact" value="{{ old('viewing_contact', $onboarding->viewing_contact ?? '') }}" placeholder="Contact person for viewings">
                <div class="help-text">Who should be contacted for viewings? (seller, tenant, etc.)</div>
            </div>
            <div class="form-group">
                <label>Preferred Viewing Times</label>
                <textarea name="preferred_viewing_times" rows="3" placeholder="Preferred viewing times (e.g., Weekdays 9am-5pm, Weekends any time)">{{ old('preferred_viewing_times', $onboarding->preferred_viewing_times ?? '') }}</textarea>
            </div>
            <div class="form-group">
                <label>Access Notes</label>
                <textarea name="access_notes" rows="4" placeholder="Alarm codes, keys location, access instructions, parking information, etc.">{{ old('access_notes', $onboarding->access_notes ?? '') }}</textarea>
                <div class="help-text">Important access information for viewings and photography</div>
            </div>
        </div>

        <!-- ID VISUAL CHECK (HMRC/EA Act Requirement) -->
        <div class="card" style="background: #fff3cd;">
            <h3>ID Visual Check <span style="font-size: 14px; font-weight: normal; color: #856404;">(Required by HMRC/EA Act)</span></h3>
            <div style="background: #fff; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                <p style="margin: 0 0 10px 0; color: #856404; font-weight: 600;">⚠️ IMPORTANT: Seller should have brought ID to the valuation appointment.</p>
                <p style="margin: 0; font-size: 13px; color: #666;">
                    Please visually check the seller's ID document (Photo ID, Passport, or Driving License) during the valuation. 
                    This is required for HMRC and Estate Agents Act compliance.
                </p>
                <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
                    <strong>Note:</strong> AML documents (photo ID + proof of address) should NOT be collected at valuation. 
                    These will be submitted via the seller dashboard after T&C signing.
                </p>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; font-weight: 600;">
                    <input type="checkbox" name="id_visual_check" value="1" {{ old('id_visual_check', $valuation->id_visual_check ?? $onboarding->id_visual_check ?? false) ? 'checked' : '' }} style="width: auto; margin-right: 8px; min-width: 20px;">
                    <span style="color: #856404;">I confirm that I have visually checked the seller's ID document on-site</span>
                </label>
            </div>
            <div class="form-group">
                <label>ID Visual Check Notes (Optional)</label>
                <textarea name="id_visual_check_notes" rows="3" placeholder="ID type checked, expiry date, any observations, etc.">{{ old('id_visual_check_notes', $valuation->id_visual_check_notes ?? $onboarding->id_visual_check_notes ?? '') }}</textarea>
                <div class="help-text">Record details about the ID document that was visually checked (e.g., "UK Passport, expires 2028")</div>
            </div>
        </div>

        <!-- VALUATION & PRICING NOTES -->
        <div class="card">
            <h3>Valuation & Pricing Notes</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Estimated Value (£)</label>
                    <input type="number" name="estimated_value" value="{{ old('estimated_value', $onboarding->estimated_value ?? $valuation->estimated_value ?? '') }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Pricing Notes</label>
                    <select name="pricing_notes">
                        <option value="">Select pricing type...</option>
                        @php
                            $selectedPricing = old('pricing_notes', $onboarding->pricing_notes ?? '');
                        @endphp
                        <option value="Offers in the Region of" {{ $selectedPricing === 'Offers in the Region of' ? 'selected' : '' }}>Offers in the Region of</option>
                        <option value="Offers in Excess of" {{ $selectedPricing === 'Offers in Excess of' ? 'selected' : '' }}>Offers in Excess of</option>
                        <option value="Guide Price" {{ $selectedPricing === 'Guide Price' ? 'selected' : '' }}>Guide Price</option>
                        <option value="Asking Price" {{ $selectedPricing === 'Asking Price' ? 'selected' : '' }}>Asking Price</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Agent Notes</label>
                <textarea name="agent_notes" rows="4" placeholder="Valuation observations, condition notes, recommendations, seller feedback, etc.">{{ $onboarding->agent_notes ?? $valuation->notes ?? '' }}</textarea>
                <div class="help-text">Any additional notes or observations from the valuation appointment</div>
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: right; padding-top: 20px; border-top: 2px solid var(--line-grey);">
            <button type="submit" class="btn btn-main">Submit Valuation Form</button>
            <a href="{{ route('admin.valuations.show', $valuation->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
        <p style="font-size: 13px; color: #666; margin-top: 15px; text-align: center;">
            <em>Submitting this form will save all information directly to the seller's profile with status "Property Details Captured".</em>
        </p>
    </form>
</div>

<script>
// Show/hide leasehold details based on tenure selection
document.querySelector('select[name="tenure"]')?.addEventListener('change', function() {
    const leaseholdDetails = document.getElementById('leasehold-details');
    if (leaseholdDetails) {
        if (this.value === 'leasehold' || this.value === 'share_freehold') {
            leaseholdDetails.style.display = 'grid';
        } else {
            leaseholdDetails.style.display = 'none';
        }
    }
});

// Initialize leasehold details visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    const tenureSelect = document.querySelector('select[name="tenure"]');
    if (tenureSelect) {
        tenureSelect.dispatchEvent(new Event('change'));
    }

    @if(config('services.google.maps_api_key'))
    // Initialize Google Places Autocomplete
    function initAddressAutocomplete() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.places === 'undefined') {
            setTimeout(initAddressAutocomplete, 100);
            return;
        }

        const addressInput = document.getElementById('property_address');
        const postcodeInput = document.getElementById('postcode');

        if (addressInput) {
            const addressAutocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address'],
                componentRestrictions: { country: 'gb' },
                fields: ['address_components', 'formatted_address']
            });

            addressAutocomplete.addListener('place_changed', function() {
                const place = addressAutocomplete.getPlace();
                
                if (!place.address_components) {
                    return;
                }

                let postcode = '';
                for (const component of place.address_components) {
                    if (component.types.includes('postal_code')) {
                        postcode = component.long_name;
                        break;
                    }
                }

                if (postcode && postcodeInput) {
                    postcodeInput.value = postcode;
                }

                addressInput.value = place.formatted_address || addressInput.value;
            });
        }

        if (postcodeInput) {
            const postcodeAutocomplete = new google.maps.places.Autocomplete(postcodeInput, {
                types: ['(regions)'],
                componentRestrictions: { country: 'gb' }
            });

            postcodeAutocomplete.addListener('place_changed', function() {
                const place = postcodeAutocomplete.getPlace();
                
                if (!place.address_components) {
                    return;
                }

                for (const component of place.address_components) {
                    if (component.types.includes('postal_code')) {
                        postcodeInput.value = component.long_name;
                        break;
                    }
                }
            });
        }
    }

    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined') {
        initAddressAutocomplete();
    } else {
        window.addEventListener('load', initAddressAutocomplete);
    }
    @endif
});
</script>
@if(config('services.google.maps_api_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" async defer></script>
@endif
@endsection
