@extends('layouts.buyer')

@section('title', 'Request a Viewing')

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
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
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

    /* INPUTS */
    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="time"],
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
    <h2>Request a Viewing</h2>
    <p class="page-subtitle">Book a viewing appointment for this property. A viewing partner will contact you to confirm.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px; text-align: left;">
            {{ session('error') }}
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

    <form action="{{ route('viewing.request.store', $property->id) }}" method="POST">
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

        <!-- VIEWING DETAILS -->
        <div class="card">
            <h3>Viewing Details</h3>
            
            <div class="grid-2">
                <div>
                    <label for="viewing_date" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Preferred Date *</label>
                    <input type="date" 
                           id="viewing_date"
                           name="viewing_date" 
                           value="{{ old('viewing_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           required
                           class="{{ $errors->has('viewing_date') ? 'error' : '' }}">
                    @error('viewing_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="viewing_time" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Preferred Time *</label>
                    <input type="time" 
                           id="viewing_time"
                           name="viewing_time" 
                           value="{{ old('viewing_time') }}"
                           required
                           class="{{ $errors->has('viewing_time') ? 'error' : '' }}">
                    @error('viewing_time')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <label for="preferred_contact_method" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Preferred Contact Method</label>
            <select name="preferred_contact_method" 
                    id="preferred_contact_method"
                    class="{{ $errors->has('preferred_contact_method') ? 'error' : '' }}">
                <option value="">Select preferred contact method (optional)</option>
                <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>Email</option>
            </select>
            @error('preferred_contact_method')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="notes" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Additional Notes (Optional)</label>
            <textarea name="notes" 
                      id="notes"
                      placeholder="Any additional information or special requirements for the viewing..."
                      class="{{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- INFORMATION -->
        <div class="card" style="background: #E8F4F3;">
            <h3>What Happens Next?</h3>
            <p>After you submit your viewing request:</p>
            <ul style="padding-left: 20px; margin: 15px 0;">
                <li>A viewing partner (PVA) will be notified of your request</li>
                <li>They will contact you to confirm the appointment</li>
                <li>You'll receive confirmation details via email</li>
            </ul>
        </div>

        <!-- SUBMIT -->
        <div class="card">
            <button type="submit" class="btn">Submit Viewing Request</button>
            <a href="{{ route('buyer.dashboard') }}" style="margin-left: 15px; color: #666; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection

