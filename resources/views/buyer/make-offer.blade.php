@extends('layouts.buyer')

@section('title', 'Make an Offer')

@push('styles')
<style>
    /* MAIN CONTAINER */
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

    /* FORM CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
        margin-bottom: 25px;
    }

    .card h3 {
        font-size: 20px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .card p {
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.6;
    }

    .card p strong {
        font-weight: 600;
    }

    .card ul {
        padding-left: 20px;
        margin: 15px 0;
    }

    .card ul li {
        margin-bottom: 8px;
        font-size: 14px;
        line-height: 1.6;
    }

    /* INPUTS */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="tel"],
    select,
    textarea {
        width: 100%;
        padding: 14px;
        margin-bottom: 18px;
        font-size: 15px;
        border: 1px solid #D9D9D9;
        border-radius: 6px;
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
        font-family: inherit;
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

    /* FILE UPLOAD */
    .upload-box {
        padding: 18px;
        border: 1px dashed #CCCCCC;
        background: var(--soft-grey);
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
    <h2>Make an Offer</h2>
    <p class="page-subtitle">Submit your offer securely. The seller will be notified immediately.</p>

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

    <form action="{{ route('buyer.offer.store', $property->id ?? 1) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- PROPERTY SUMMARY -->
        <div class="card">
            <h3>Property</h3>
            <p><strong>Address:</strong> {{ $property->address }}</p>
            @if($property->postcode)
                <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
            @endif
            <p><strong>Asking Price:</strong> £{{ number_format($property->asking_price, 2) }}</p>
            @if($property->property_type)
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</p>
            @endif
            @if($property->bedrooms)
                <p><strong>Bedrooms:</strong> {{ $property->bedrooms }}</p>
            @endif
        </div>

        <!-- BUYER INFORMATION (Auto-filled) -->
        <div class="card" style="background: #E8F4F3;">
            <h3>Your Information</h3>
            <p><strong>Name:</strong> {{ $user->name ?? auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ $user->email ?? auth()->user()->email }}</p>
            @if($user->phone ?? auth()->user()->phone)
                <p><strong>Phone:</strong> {{ $user->phone ?? auth()->user()->phone }}</p>
            @endif
            <p style="font-size: 13px; color: #666; margin-top: 10px;">
                This information will be shared with the seller when you submit your offer.
            </p>
        </div>

        <!-- OFFER DETAILS -->
        <div class="card">
            <h3>Your Offer</h3>
            <input type="number" 
                   name="offer_amount" 
                   placeholder="Offer amount (£)" 
                   value="{{ old('offer_amount') }}"
                   min="0"
                   step="1000"
                   required
                   class="{{ $errors->has('offer_amount') ? 'error' : '' }}">
            @error('offer_amount')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="funding_type" 
                    required
                    class="{{ $errors->has('funding_type') ? 'error' : '' }}">
                <option value="">How will you fund the purchase?</option>
                <option value="cash" {{ old('funding_type') == 'cash' ? 'selected' : '' }}>Cash buyer</option>
                <option value="mortgage" {{ old('funding_type') == 'mortgage' ? 'selected' : '' }}>Mortgage</option>
                <option value="combination" {{ old('funding_type') == 'combination' ? 'selected' : '' }}>Combination of cash + mortgage</option>
            </select>
            @error('funding_type')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="number" 
                   name="deposit_amount" 
                   placeholder="Deposit amount (£)" 
                   value="{{ old('deposit_amount') }}"
                   min="0"
                   step="1000"
                   class="{{ $errors->has('deposit_amount') ? 'error' : '' }}">
            @error('deposit_amount')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <select name="buying_position" 
                    required
                    class="{{ $errors->has('buying_position') ? 'error' : '' }}">
                <option value="">Your buying position</option>
                <option value="first-time-buyer" {{ old('buying_position') == 'first-time-buyer' ? 'selected' : '' }}>First-time buyer</option>
                <option value="renting" {{ old('buying_position') == 'renting' ? 'selected' : '' }}>Renting</option>
                <option value="living-with-family" {{ old('buying_position') == 'living-with-family' ? 'selected' : '' }}>Living with family</option>
                <option value="sold-sstc" {{ old('buying_position') == 'sold-sstc' ? 'selected' : '' }}>Sold (SSTC)</option>
                <option value="cash-buyer" {{ old('buying_position') == 'cash-buyer' ? 'selected' : '' }}>Cash buyer</option>
                <option value="investor-btl" {{ old('buying_position') == 'investor-btl' ? 'selected' : '' }}>Investor / Buy-to-let</option>
            </select>
            @error('buying_position')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <textarea name="conditions" 
                      placeholder="Conditions of your offer (optional, e.g., subject to survey)"
                      class="{{ $errors->has('conditions') ? 'error' : '' }}">{{ old('conditions') }}</textarea>
            @error('conditions')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SUPPORTING DOCUMENTS -->
        <div class="card">
            <h3>Supporting Documents</h3>
            <div class="upload-box">
                <strong>Proof of funds</strong>
                <input type="file" 
                       name="proof_of_funds" 
                       accept="image/*,.pdf"
                       class="{{ $errors->has('proof_of_funds') ? 'error' : '' }}">
            </div>
            @error('proof_of_funds')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="upload-box">
                <strong>Mortgage Agreement in Principle (if applicable)</strong>
                <input type="file" 
                       name="agreement_in_principle" 
                       accept="image/*,.pdf"
                       class="{{ $errors->has('agreement_in_principle') ? 'error' : '' }}">
            </div>
            @error('agreement_in_principle')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- SOLICITOR DETAILS -->
        <div class="card">
            <h3>Your Solicitor</h3>
            <input type="text" 
                   name="solicitor_name" 
                   placeholder="Solicitor name (if known)" 
                   value="{{ old('solicitor_name') }}"
                   class="{{ $errors->has('solicitor_name') ? 'error' : '' }}">
            @error('solicitor_name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="text" 
                   name="solicitor_firm" 
                   placeholder="Firm name" 
                   value="{{ old('solicitor_firm') }}"
                   class="{{ $errors->has('solicitor_firm') ? 'error' : '' }}">
            @error('solicitor_firm')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="email" 
                   name="solicitor_email" 
                   placeholder="Solicitor email" 
                   value="{{ old('solicitor_email') }}"
                   class="{{ $errors->has('solicitor_email') ? 'error' : '' }}">
            @error('solicitor_email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="tel" 
                   name="solicitor_phone" 
                   placeholder="Solicitor phone number" 
                   value="{{ old('solicitor_phone') }}"
                   class="{{ $errors->has('solicitor_phone') ? 'error' : '' }}">
            @error('solicitor_phone')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- DECLARATION -->
        <div class="card">
            <h3>Declaration</h3>
            <p>By submitting this offer, you confirm that:</p>
            <ul>
                <li>The information provided is accurate and truthful.</li>
                <li>You understand the seller may request further verification.</li>
                <li>Your offer does not constitute a legally binding contract.</li>
            </ul>
            <button type="submit" class="btn">Submit Offer</button>
        </div>
    </form>
</div>
@endsection
