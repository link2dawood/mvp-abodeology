@extends('layouts.seller')

@section('title', 'Instruct Abodeology')

@push('styles')
<style>
    /* CONTENT */
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
        margin-bottom: 25px;
        color: #666;
    }

    /* CARD BLOCK */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
        margin-bottom: 30px;
    }

    .card h3 {
        margin-top: 0;
        font-size: 20px;
        margin-bottom: 15px;
    }

    /* TEXT */
    .text-block {
        line-height: 1.6em;
        color: #444;
        margin-bottom: 20px;
    }

    ul {
        padding-left: 20px;
        margin: 15px 0;
    }

    ul li {
        margin-bottom: 10px;
        line-height: 1.6;
    }

    /* INPUTS */
    input[type="text"],
    input[type="email"],
    input[type="date"] {
        width: 100%;
        padding: 14px;
        margin-bottom: 18px;
        border-radius: 6px;
        border: 1px solid #D9D9D9;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
    }

    input:focus {
        border-color: var(--abodeology-teal);
    }

    input.error {
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

    /* CHECKBOXES */
    .checkbox-block {
        margin-bottom: 18px;
        display: flex;
        align-items: flex-start;
    }

    .checkbox-block input[type="checkbox"] {
        margin-right: 10px;
        margin-top: 3px;
        cursor: pointer;
        width: auto;
        padding: 0;
    }

    .checkbox-block label {
        cursor: pointer;
        line-height: 1.6;
        font-size: 14px;
    }

    /* HR */
    hr {
        margin: 25px 0;
        border: 0;
        border-bottom: 1px solid var(--line-grey);
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

    .btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Instruct Abodeology</h2>
    <p class="page-subtitle">Please review your agreement details below before digitally signing.</p>

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

    <form action="{{ isset($property) && $property ? route('seller.instruct.store', $property->id) : route('seller.instruct.store.general') }}" method="POST" id="instructForm">
        @csrf

        <!-- AGREEMENT SUMMARY -->
        <div class="card">
            <h3>Your Instruction Summary</h3>
            <div class="text-block">
                <strong>Property:</strong> {{ $instruction->property_address ?? '123 Main Street, London, SW1A 1AA' }}<br>
                <strong>Sellers:</strong> {{ $instruction->seller_names ?? auth()->user()->name }}<br>
                <strong>Agency Type:</strong> Sole Agency<br>
                <strong id="fee-display">Commission Fee:</strong> <span id="fee-percentage">{{ $instruction->fee_percentage ?? '1.5' }}</span>% + VAT
                <span id="fee-reduction-note" style="display: {{ ($instruction->self_host_viewings ?? false) ? 'inline' : 'none' }}; color: #28a745; font-weight: 600; margin-left: 10px;">(Reduced fee for self-hosted viewings)</span>
            </div>
        </div>

        <!-- VIEWING HOSTING OPTION -->
        <div class="card" style="background: #F8F9FA;">
            <h3 style="margin-top: 0;">Viewing Arrangements</h3>
            <div class="text-block">
                <p style="margin-bottom: 15px;">
                    <strong>How would you like viewings to be conducted?</strong>
                </p>
                <div class="checkbox-block" style="margin-bottom: 15px;">
                    <input type="radio" name="self_host_viewings" id="viewing_abodeology" value="0" 
                           {{ old('self_host_viewings', $instruction->self_host_viewings ?? false) ? '' : 'checked' }}
                           class="{{ $errors->has('self_host_viewings') ? 'error' : '' }}"
                           onchange="updateFeeDisplay()">
                    <label for="viewing_abodeology">
                        <strong>Abodeology-hosted viewings</strong> (Standard fee)<br>
                        <span style="font-size: 13px; color: #666;">Our Agents and Property Viewing Assistants (PVAs) will conduct all viewings on your behalf.</span>
                    </label>
                </div>
                <div class="checkbox-block">
                    <input type="radio" name="self_host_viewings" id="viewing_self_host" value="1"
                           {{ old('self_host_viewings', $instruction->self_host_viewings ?? false) ? 'checked' : '' }}
                           class="{{ $errors->has('self_host_viewings') ? 'error' : '' }}"
                           onchange="updateFeeDisplay()">
                    <label for="viewing_self_host">
                        <strong>Self-hosted viewings</strong> (Reduced fee)<br>
                        <span style="font-size: 13px; color: #666;">You will conduct viewings yourself. This reflects your contribution to the process and results in a reduced commission fee.</span>
                    </label>
                </div>
            </div>
            @error('self_host_viewings')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- TERMS & CONDITIONS PDF -->
        <div class="card">
            <h3>Terms & Conditions of Business</h3>
            <div class="text-block">
                <p style="margin-bottom: 15px;">
                    <strong>Please review the full Terms & Conditions document below before signing.</strong>
                </p>
                <p style="margin-bottom: 20px; color: #856404; font-weight: 600;">
                    ⚠️ You must read and understand the Terms & Conditions before proceeding with your signature.
                </p>
                
                <!-- PDF Preview -->
                @php
                    if (isset($property) && $property) {
                        $htmlUrl = route('seller.terms-html', $property->id);
                        $pdfUrl = route('seller.terms-pdf', $property->id);
                    } else {
                        $htmlUrl = request()->getSchemeAndHttpHost() . '/terms-and-conditions.pdf';
                        $pdfUrl = request()->getSchemeAndHttpHost() . '/terms-and-conditions.pdf';
                    }
                @endphp
                <div style="border: 2px solid var(--line-grey); border-radius: 8px; padding: 15px; background: #f9f9f9; margin-bottom: 20px;">
                    <iframe 
                        src="{{ $pdfUrl }}" 
                        width="100%" 
                        height="600px" 
                        style="border: 1px solid #ddd; border-radius: 4px;"
                        title="Terms and Conditions Preview"
                        type="application/pdf">
                        <p style="padding: 20px; text-align: center;">
                            Your browser does not support iframes. 
                            <a href="{{ $pdfUrl }}" target="_blank" style="color: var(--abodeology-teal); text-decoration: underline;">
                                Click here to view the Terms & Conditions
                            </a>
                        </p>
                    </iframe>
                </div>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener noreferrer" class="btn" style="background: var(--abodeology-teal);">
                        Open PDF in New Window
                    </a>
                </div>
            </div>
        </div>

        <!-- KEY TERMS SUMMARY -->
        <div class="card" style="background: #E8F4F3;">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">Key Terms Summary</h3>
            <div class="text-block">
                <strong>Important:</strong><br>
                • You have a <strong>14-day cancellation right</strong> under Consumer Contract Regulations.<br>
                • If you request immediate marketing, you may still be liable for fees if we introduce a buyer during the cancellation period.<br><br>
                Abodeology operates in full compliance with:
                <ul>
                    <li>Estate Agents Act 1979</li>
                    <li>CPRs & Material Information Requirements</li>
                    <li>Anti-Money Laundering (AML) Regulations</li>
                    <li>GDPR & Data Protection Act 2018</li>
                </ul>
            </div>
        </div>

        <!-- HOMECHECK -->
        <div class="card">
            <h3>Abodeology HomeCheck</h3>
            <div class="text-block">
                Where selected, the Abodeology® HomeCheck uses a data collection process
                carried out by Abodeology, analysed by independent third-party providers using AI-assisted tools.
                <br><br>
                All inspections and analysis remain the responsibility of the third-party provider, not Abodeology.
            </div>
        </div>

        <!-- DECLARATIONS -->
        <div class="card">
            <h3>Declarations</h3>
            <div class="checkbox-block">
                <input type="checkbox" name="declaration_accurate" id="declaration_accurate" value="1" required
                       class="{{ $errors->has('declaration_accurate') ? 'error' : '' }}">
                <label for="declaration_accurate">I confirm that all information provided is accurate.</label>
            </div>
            @error('declaration_accurate')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="checkbox-block">
                <input type="checkbox" name="declaration_legal_entitlement" id="declaration_legal_entitlement" value="1" required
                       class="{{ $errors->has('declaration_legal_entitlement') ? 'error' : '' }}">
                <label for="declaration_legal_entitlement">I confirm I am legally entitled to instruct the sale of this property.</label>
            </div>
            @error('declaration_legal_entitlement')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="checkbox-block">
                <input type="checkbox" name="declaration_immediate_marketing" id="declaration_immediate_marketing" value="1"
                       class="{{ $errors->has('declaration_immediate_marketing') ? 'error' : '' }}">
                <label for="declaration_immediate_marketing">I authorise Abodeology to begin marketing immediately.</label>
            </div>
            @error('declaration_immediate_marketing')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="checkbox-block">
                <input type="checkbox" name="declaration_terms" id="declaration_terms" value="1" required
                       class="{{ $errors->has('declaration_terms') ? 'error' : '' }}">
                <label for="declaration_terms">
                    I have read and accept the <strong>Estate Agency Terms & Conditions of Business</strong> 
                    (PDF document displayed above). By checking this box, I confirm I have reviewed the full terms and agree to be bound by them.
                </label>
            </div>
            @error('declaration_terms')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="checkbox-block">
                <input type="checkbox" name="declaration_homecheck" id="declaration_homecheck" value="1"
                       class="{{ $errors->has('declaration_homecheck') ? 'error' : '' }}">
                <label for="declaration_homecheck">I acknowledge the HomeCheck disclaimer (if applicable).</label>
            </div>
            @error('declaration_homecheck')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SIGNATURE -->
        <div class="card">
            <h3>Digital Signature</h3>
            <input type="text" 
                   name="seller1_name" 
                   placeholder="Seller 1 Full Name" 
                   value="{{ @$instruction->seller1_name ?? '' }}"
                   required
                   class="{{ $errors->has('seller1_name') ? 'error' : '' }}">
            @error('seller1_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="seller1_signature" 
                   placeholder="Digital Signature (type your full name)" 
                   value="{{ @$instruction->seller1_signature ?? '' }}"
                   required
                   class="{{ $errors->has('seller1_signature') ? 'error' : '' }}">
            @error('seller1_signature')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="date" 
                   name="seller1_date" 
                   value="{{ old('seller1_date', date('Y-m-d')) }}"
                   required
                   class="{{ $errors->has('seller1_date') ? 'error' : '' }}">
            @error('seller1_date')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <hr>

            <input type="text" 
                   name="seller2_name" 
                   placeholder="Seller 2 Full Name (optional)" 
                   value="{{ @$instruction->seller2_name ?? '' }}"
                   class="{{ $errors->has('seller2_name') ? 'error' : '' }}">
            @error('seller2_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="seller2_signature" 
                   placeholder="Digital Signature (type your full name)" 
                   value="{{@$instruction->seller2_signature ?? '' }}"
                   class="{{ $errors->has('seller2_signature') ? 'error' : '' }}">
            @error('seller2_signature')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="date" 
                   name="seller2_date" 
                   value="{{ old('seller2_date', date('Y-m-d')) }}"
                   class="{{ $errors->has('seller2_date') ? 'error' : '' }}">
            @error('seller2_date')
                <div class="error-message">{{ $message }}</div>
            @enderror
            
            @if(isset($property) && $property)
            <div style="margin-top: 20px; text-align: center;">
                <button type="button" class="btn" id="saveSignatureBtn" style="background: var(--abodeology-teal);">
                    Save
                </button>
            </div>
            @endif
        </div>
       

        <!-- HIDDEN PROPERTY ID -->
        @if(isset($property) && $property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
        @endif

        <!-- SUBMIT -->
        <button type="submit" class="btn" id="submitBtn">Sign Up Now</button>
    </form>
    
    <!-- Hidden form for saving signatures only (outside main form) -->
    @if(isset($property) && $property)
    <form id="signatureForm" action="{{ route('seller.signature.save', $property->id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="seller1_name" id="signature_seller1_name" value="">
        <input type="hidden" name="seller1_signature" id="signature_seller1_signature" value="">
        <input type="hidden" name="seller1_date" id="signature_seller1_date" value="">
        <input type="hidden" name="seller2_name" id="signature_seller2_name" value="">
        <input type="hidden" name="seller2_signature" id="signature_seller2_signature" value="">
        <input type="hidden" name="seller2_date" id="signature_seller2_date" value="">
    </form>
    @endif
</div>

@push('scripts')
<script>
    // Fee calculation: Standard 1.5%, Reduced 1.25% (0.25% reduction for self-hosted)
    const STANDARD_FEE = 1.5;
    const REDUCED_FEE = 1.25;
    const FEE_REDUCTION = 0.25;

    function updateFeeDisplay() {
        const selfHost = document.getElementById('viewing_self_host').checked;
        const feeDisplay = document.getElementById('fee-percentage');
        const reductionNote = document.getElementById('fee-reduction-note');
        
        if (selfHost) {
            feeDisplay.textContent = REDUCED_FEE.toFixed(2);
            reductionNote.style.display = 'inline';
        } else {
            feeDisplay.textContent = STANDARD_FEE.toFixed(2);
            reductionNote.style.display = 'none';
        }
    }

    // Initialize fee display on page load based on current selection
    document.addEventListener('DOMContentLoaded', function() {
        const selfHost = document.getElementById('viewing_self_host').checked;
        const feeDisplay = document.getElementById('fee-percentage');
        const reductionNote = document.getElementById('fee-reduction-note');
        
        // Set initial fee based on saved preference or default
        if (selfHost) {
            feeDisplay.textContent = REDUCED_FEE.toFixed(2);
            reductionNote.style.display = 'inline';
        } else {
            feeDisplay.textContent = STANDARD_FEE.toFixed(2);
            reductionNote.style.display = 'none';
        }
    });

    document.getElementById('instructForm').addEventListener('submit', function(e) {
        // Validate all required checkboxes
        const requiredCheckboxes = [
            'declaration_accurate',
            'declaration_legal_entitlement',
            'declaration_terms'
        ];
        
        let allChecked = true;
        requiredCheckboxes.forEach(function(id) {
            if (!document.getElementById(id).checked) {
                allChecked = false;
            }
        });
        
        if (!allChecked) {
            e.preventDefault();
            alert('Please accept all required declarations before submitting.');
            return false;
        }
        
        // Confirm submission
        if (!confirm('Are you sure you want to submit your instruction? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Save signature button handler
    document.addEventListener('DOMContentLoaded', function() {
        const saveSignatureBtn = document.getElementById('saveSignatureBtn');
        const signatureForm = document.getElementById('signatureForm');
        
        if (!saveSignatureBtn) {
            return; // Button doesn't exist, exit
        }
        
        if (!signatureForm) {
            console.error('Signature form not found in DOM');
            saveSignatureBtn.style.display = 'none'; // Hide button if form doesn't exist
            return;
        }
        
        saveSignatureBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Use setTimeout to prevent blocking the UI
            setTimeout(function() {
                const form = document.getElementById('signatureForm');
                if (!form) {
                    alert('Error: Signature form not found. Please refresh the page and try again.');
                    return;
                }
                
                // Get values from main form
                const seller1NameInput = document.querySelector('input[name="seller1_name"]');
                const seller1SignatureInput = document.querySelector('input[name="seller1_signature"]');
                const seller1DateInput = document.querySelector('input[name="seller1_date"]');
                const seller2NameInput = document.querySelector('input[name="seller2_name"]');
                const seller2SignatureInput = document.querySelector('input[name="seller2_signature"]');
                const seller2DateInput = document.querySelector('input[name="seller2_date"]');
                
                const seller1Name = seller1NameInput ? seller1NameInput.value.trim() : '';
                const seller1Signature = seller1SignatureInput ? seller1SignatureInput.value.trim() : '';
                const seller1Date = seller1DateInput ? seller1DateInput.value : '';
                const seller2Name = seller2NameInput ? seller2NameInput.value.trim() : '';
                const seller2Signature = seller2SignatureInput ? seller2SignatureInput.value.trim() : '';
                const seller2Date = seller2DateInput ? seller2DateInput.value : '';
                
                // Validate required fields
                if (!seller1Name || !seller1Signature || !seller1Date) {
                    alert('Please fill in all required signature fields for Seller 1.');
                    return;
                }
                
                // Get hidden form inputs
                const hiddenSeller1Name = document.getElementById('signature_seller1_name');
                const hiddenSeller1Signature = document.getElementById('signature_seller1_signature');
                const hiddenSeller1Date = document.getElementById('signature_seller1_date');
                const hiddenSeller2Name = document.getElementById('signature_seller2_name');
                const hiddenSeller2Signature = document.getElementById('signature_seller2_signature');
                const hiddenSeller2Date = document.getElementById('signature_seller2_date');
                
                // Check if all hidden inputs exist
                if (!hiddenSeller1Name || !hiddenSeller1Signature || !hiddenSeller1Date || 
                    !hiddenSeller2Name || !hiddenSeller2Signature || !hiddenSeller2Date) {
                    alert('Error: Signature form fields not found. Please refresh the page and try again.');
                    return;
                }
                
                // Disable button to prevent double submission
                saveSignatureBtn.disabled = true;
                saveSignatureBtn.textContent = 'Saving...';
                
                // Set values in hidden form
                hiddenSeller1Name.value = seller1Name;
                hiddenSeller1Signature.value = seller1Signature;
                hiddenSeller1Date.value = seller1Date;
                hiddenSeller2Name.value = seller2Name;
                hiddenSeller2Signature.value = seller2Signature;
                hiddenSeller2Date.value = seller2Date;
                
                // Submit the signature form
                form.submit();
            }, 0);
        });
    });
</script>
@endpush
@endsection
