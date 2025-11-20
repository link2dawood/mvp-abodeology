@extends('layouts.buyer')

@section('title', 'Buyer Profile')

@push('styles')
<style>
    /* CONTAINER */
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        margin-bottom: 8px;
        font-size: 28px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 28px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: 600;
    }

    .card p {
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.6;
    }

    /* FORM ELEMENTS */
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
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
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

    textarea {
        height: 120px;
        resize: vertical;
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

    /* UPLOAD FIELD */
    .upload-box {
        background: var(--soft-grey);
        border: 1px dashed #CCCCCC;
        padding: 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .upload-box input[type="file"] {
        margin-top: 10px;
        width: auto;
        padding: 8px;
    }

    .upload-box strong {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
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
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn:active {
        transform: scale(0.98);
    }

    /* GRID */
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Your Buyer Profile</h2>
    <p class="page-subtitle">Complete your information so you are fully verified and offer-ready.</p>

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

    <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- PERSONAL DETAILS -->
        <div class="card">
            <h3>Personal Details</h3>
            <input type="text" 
                   name="name" 
                   placeholder="Full name" 
                   value="{{ old('name', $user->name ?? '') }}"
                   required
                   class="{{ $errors->has('name') ? 'error' : '' }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="email" 
                   name="email" 
                   placeholder="Email address" 
                   value="{{ old('email', $user->email ?? '') }}"
                   required
                   class="{{ $errors->has('email') ? 'error' : '' }}">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="tel" 
                   name="phone" 
                   placeholder="Mobile number" 
                   value="{{ old('phone', $user->phone ?? '') }}"
                   required
                   class="{{ $errors->has('phone') ? 'error' : '' }}">
            @error('phone')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- AML DOCUMENTS -->
        <div class="card">
            <h3>Identity Verification (AML)</h3>
            <p>Please upload the following documents:</p>
            
            <div class="upload-box">
                <strong>Photo ID (passport or driving licence)</strong>
                <input type="file" name="photo_id" accept="image/*,.pdf">
            </div>

            <div class="upload-box">
                <strong>Proof of address (dated within 3 months)</strong>
                <input type="file" name="proof_of_address" accept="image/*,.pdf">
            </div>

            <div class="upload-box">
                <strong>Proof of funds (bank statement or agreement in principle)</strong>
                <input type="file" name="proof_of_funds" accept="image/*,.pdf">
            </div>
        </div>

        <!-- BUYING POSITION -->
        <div class="card">
            <h3>Your Buying Position</h3>
            <select name="buying_position" class="{{ $errors->has('buying_position') ? 'error' : '' }}">
                <option value="">Select your buying position</option>
                <option value="first-time-buyer" {{ old('buying_position', $buyerProfile->buying_position ?? '') == 'first-time-buyer' ? 'selected' : '' }}>First-time buyer</option>
                <option value="renting" {{ old('buying_position', $buyerProfile->buying_position ?? '') == 'renting' ? 'selected' : '' }}>Renting</option>
                <option value="sold-sstc" {{ old('buying_position', $buyerProfile->buying_position ?? '') == 'sold-sstc' ? 'selected' : '' }}>Sold (SSTC)</option>
                <option value="cash-buyer" {{ old('buying_position', $buyerProfile->buying_position ?? '') == 'cash-buyer' ? 'selected' : '' }}>Cash buyer</option>
                <option value="investor-btl" {{ old('buying_position', $buyerProfile->buying_position ?? '') == 'investor-btl' ? 'selected' : '' }}>Investor / Buy-to-let</option>
            </select>
            @error('buying_position')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="requires_mortgage" class="{{ $errors->has('requires_mortgage') ? 'error' : '' }}">
                <option value="">Do you require a mortgage?</option>
                <option value="yes" {{ old('requires_mortgage', $buyerProfile->requires_mortgage ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('requires_mortgage', $buyerProfile->requires_mortgage ?? '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
            @error('requires_mortgage')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="agreement_in_principle" 
                   placeholder="If yes, do you have an Agreement in Principle?" 
                   value="{{ old('agreement_in_principle', $buyerProfile->agreement_in_principle ?? '') }}"
                   class="{{ $errors->has('agreement_in_principle') ? 'error' : '' }}">
            @error('agreement_in_principle')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- FINANCIAL DETAILS -->
        <div class="card">
            <h3>Your Financial Details</h3>
            <input type="number" 
                   name="deposit_amount" 
                   placeholder="Deposit amount (£)" 
                   value="{{ old('deposit_amount', $buyerProfile->deposit_amount ?? '') }}"
                   min="0"
                   step="1000"
                   class="{{ $errors->has('deposit_amount') ? 'error' : '' }}">
            @error('deposit_amount')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="number" 
                   name="max_budget" 
                   placeholder="Maximum budget (£)" 
                   value="{{ old('max_budget', $buyerProfile->max_budget ?? '') }}"
                   min="0"
                   step="1000"
                   class="{{ $errors->has('max_budget') ? 'error' : '' }}">
            @error('max_budget')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="financial_notes" 
                      placeholder="Additional financial notes (optional)"
                      class="{{ $errors->has('financial_notes') ? 'error' : '' }}">{{ old('financial_notes', $buyerProfile->financial_notes ?? '') }}</textarea>
            @error('financial_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SOLICITOR DETAILS -->
        <div class="card">
            <h3>Your Solicitor</h3>
            <input type="text" 
                   name="solicitor_name" 
                   placeholder="Solicitor name" 
                   value="{{ old('solicitor_name', $buyerProfile->solicitor_name ?? '') }}"
                   class="{{ $errors->has('solicitor_name') ? 'error' : '' }}">
            @error('solicitor_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="solicitor_firm" 
                   placeholder="Firm name" 
                   value="{{ old('solicitor_firm', $buyerProfile->solicitor_firm ?? '') }}"
                   class="{{ $errors->has('solicitor_firm') ? 'error' : '' }}">
            @error('solicitor_firm')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="email" 
                   name="solicitor_email" 
                   placeholder="Solicitor email" 
                   value="{{ old('solicitor_email', $buyerProfile->solicitor_email ?? '') }}"
                   class="{{ $errors->has('solicitor_email') ? 'error' : '' }}">
            @error('solicitor_email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="tel" 
                   name="solicitor_phone" 
                   placeholder="Solicitor phone number" 
                   value="{{ old('solicitor_phone', $buyerProfile->solicitor_phone ?? '') }}"
                   class="{{ $errors->has('solicitor_phone') ? 'error' : '' }}">
            @error('solicitor_phone')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" class="btn">Save Profile</button>
    </form>
</div>
@endsection
