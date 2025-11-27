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
                <strong>Commission Fee:</strong> {{ $instruction->fee_percentage ?? '1.5' }}% + VAT
            </div>
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
                
                <!-- PDF Viewer -->
                <div style="border: 2px solid var(--line-grey); border-radius: 8px; padding: 15px; background: #f9f9f9; margin-bottom: 20px;">
                    <iframe 
                        src="{{ asset('terms-and-conditions.pdf') }}" 
                        width="100%" 
                        height="600px" 
                        style="border: 1px solid #ddd; border-radius: 4px;"
                        title="Terms and Conditions PDF">
                        <p style="padding: 20px; text-align: center;">
                            Your browser does not support PDFs. 
                            <a href="{{ asset('terms-and-conditions.pdf') }}" target="_blank" style="color: var(--abodeology-teal); text-decoration: underline;">
                                Click here to download the Terms & Conditions PDF
                            </a>
                        </p>
                    </iframe>
                </div>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="{{ asset('terms-and-conditions.pdf') }}" target="_blank" class="btn" style="background: var(--abodeology-teal);">
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
                   value="{{ old('seller1_name', auth()->user()->name ?? '') }}"
                   required
                   class="{{ $errors->has('seller1_name') ? 'error' : '' }}">
            @error('seller1_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="seller1_signature" 
                   placeholder="Digital Signature (type your full name)" 
                   value="{{ old('seller1_signature') }}"
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
                   value="{{ old('seller2_name') }}"
                   class="{{ $errors->has('seller2_name') ? 'error' : '' }}">
            @error('seller2_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="seller2_signature" 
                   placeholder="Digital Signature (type your full name)" 
                   value="{{ old('seller2_signature') }}"
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
        </div>

        <!-- HIDDEN PROPERTY ID -->
        @if(isset($property) && $property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
        @endif

        <!-- SUBMIT -->
        <button type="submit" class="btn" id="submitBtn">Sign Up Now</button>
    </form>
</div>

@push('scripts')
<script>
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
</script>
@endpush
@endsection
