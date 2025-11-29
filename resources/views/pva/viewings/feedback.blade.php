@extends('layouts.pva')

@section('title', 'Submit Viewing Feedback')

@push('styles')
<style>
    .container {
        max-width: 800px;
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

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
        margin-bottom: 25px;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: 600;
    }

    .info-box {
        background: #E8F4F3;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
    }

    .info-box strong {
        color: var(--abodeology-teal);
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
        color: #1E1E1E;
    }

    input[type="text"],
    input[type="email"],
    textarea,
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #D9D9D9;
        border-radius: 6px;
        font-size: 15px;
        margin-bottom: 18px;
        box-sizing: border-box;
        font-family: inherit;
    }

    textarea {
        height: 120px;
        resize: vertical;
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
    }

    .radio-group {
        margin-bottom: 20px;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        font-weight: normal;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .radio-group input[type="radio"] {
        width: auto;
        margin-right: 10px;
        margin-bottom: 0;
    }

    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 14px 28px;
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

    .btn-secondary {
        background: transparent;
        color: var(--abodeology-teal);
        border: 2px solid var(--abodeology-teal);
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
    }

    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 20px;
        color: #155724;
        font-size: 14px;
    }

    .alert-error {
        background: #fee;
        border: 1px solid #dc3545;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 20px;
        color: #dc3545;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Submit Viewing Feedback</h2>
    <p class="page-subtitle">Provide feedback about the viewing and buyer's interest level.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <strong>Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- VIEWING INFORMATION -->
    <div class="card">
        <h3>Viewing Details</h3>
        <div class="info-box">
            <p><strong>Property:</strong> {{ $viewing->property->address ?? 'N/A' }}</p>
            <p><strong>Buyer:</strong> {{ $viewing->buyer->name ?? 'N/A' }}</p>
            @if($viewing->buyer && $viewing->buyer->email)
                <p><strong>Buyer Email:</strong> {{ $viewing->buyer->email }}</p>
            @endif
            <p><strong>Viewing Date:</strong> {{ $viewing->viewing_date ? $viewing->viewing_date->format('l, F j, Y g:i A') : 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($viewing->status ?? 'Scheduled') }}</p>
        </div>
    </div>

    <!-- FEEDBACK FORM -->
    <form action="{{ route('pva.viewings.feedback.store', $viewing->id) }}" method="POST">
        @csrf

        <div class="card">
            <h3>Buyer Interest</h3>
            
            <div class="radio-group">
                <label>
                    <input type="radio" name="buyer_interested" value="1" {{ old('buyer_interested', $viewing->feedback->buyer_interested ?? null) == 1 ? 'checked' : '' }} required>
                    <span>Buyer is interested in the property</span>
                </label>
                <label>
                    <input type="radio" name="buyer_interested" value="0" {{ old('buyer_interested', $viewing->feedback->buyer_interested ?? null) == 0 ? 'checked' : '' }} required>
                    <span>Buyer is not interested in the property</span>
                </label>
            </div>
            @error('buyer_interested')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <h3>Buyer Feedback</h3>
            
            <label for="buyer_feedback">What did the buyer say about the property?</label>
            <textarea 
                name="buyer_feedback" 
                id="buyer_feedback"
                placeholder="Enter buyer's comments and feedback about the property..."
            >{{ old('buyer_feedback', $viewing->feedback->buyer_feedback ?? '') }}</textarea>
            @error('buyer_feedback')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <h3>Property Condition</h3>
            
            <label for="property_condition">How would you rate the property's condition?</label>
            <select name="property_condition" id="property_condition">
                <option value="">Select condition...</option>
                <option value="excellent" {{ old('property_condition', $viewing->feedback->property_condition ?? '') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                <option value="good" {{ old('property_condition', $viewing->feedback->property_condition ?? '') == 'good' ? 'selected' : '' }}>Good</option>
                <option value="fair" {{ old('property_condition', $viewing->feedback->property_condition ?? '') == 'fair' ? 'selected' : '' }}>Fair</option>
                <option value="poor" {{ old('property_condition', $viewing->feedback->property_condition ?? '') == 'poor' ? 'selected' : '' }}>Poor</option>
            </select>
            @error('property_condition')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <h3>Additional Notes</h3>
            
            <label for="buyer_notes">Buyer Notes (optional)</label>
            <textarea 
                name="buyer_notes" 
                id="buyer_notes"
                placeholder="Any additional notes about the buyer or their requirements..."
            >{{ old('buyer_notes', $viewing->feedback->buyer_notes ?? '') }}</textarea>
            @error('buyer_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="pva_notes" style="margin-top: 20px;">PVA Notes (optional)</label>
            <textarea 
                name="pva_notes" 
                id="pva_notes"
                placeholder="Your internal notes about the viewing..."
            >{{ old('pva_notes', $viewing->feedback->pva_notes ?? '') }}</textarea>
            @error('pva_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <button type="submit" class="btn">Submit Feedback</button>
            <a href="{{ route('pva.viewings.show', $viewing->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

