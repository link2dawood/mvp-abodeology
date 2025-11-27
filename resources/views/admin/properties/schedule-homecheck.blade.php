@extends('layouts.admin')

@section('title', 'Schedule HomeCheck')

@push('styles')
<style>
    .container {
        max-width: 700px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
    }

    .card {
        border: 1px solid #dcdcdc;
        padding: 25px;
        margin: 20px 0;
        border-radius: 4px;
        background: #fff;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }

    input[type="date"],
    textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        font-size: 15px;
        box-sizing: border-box;
        font-family: inherit;
    }

    textarea {
        height: 120px;
        resize: vertical;
    }

    input:focus,
    textarea:focus {
        border-color: #2CB8B4;
        outline: none;
    }

    input.error,
    textarea.error {
        border-color: #dc3545;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #1E1E1E;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .btn-main {
        background: #2CB8B4;
    }

    .info-box {
        background: #E8F4F3;
        border-left: 4px solid #2CB8B4;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #1E1E1E;
    }

    .warning-box {
        background: #fff3cd;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .warning-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #856404;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Schedule HomeCheck</h2>
        <a href="{{ route('admin.properties.show', $property->id) }}" class="btn" style="background: #666;">← Back to Property</a>
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

    <div class="info-box">
        <p><strong>Property:</strong> {{ $property->address }}</p>
        <p>Schedule an Abodeology HomeCheck appointment to capture 360° images and photos of the property.</p>
    </div>

    @if($existingHomeCheck)
        <div class="warning-box">
            <p><strong>⚠️ Notice:</strong> A HomeCheck is already {{ $existingHomeCheck->status === 'scheduled' ? 'scheduled' : 'in progress' }} for this property.</p>
            @if($existingHomeCheck->scheduled_date)
                <p><strong>Scheduled Date:</strong> {{ \Carbon\Carbon::parse($existingHomeCheck->scheduled_date)->format('l, F j, Y') }}</p>
            @endif
            <p>You can only schedule one HomeCheck at a time. Please complete or cancel the existing HomeCheck first.</p>
        </div>
    @endif

    <form action="{{ route('admin.properties.schedule-homecheck.store', $property->id) }}" method="POST">
        @csrf

        <div class="card">
            <h3>HomeCheck Appointment Details</h3>

            <div>
                <label for="scheduled_date">Scheduled Date *</label>
                <input type="date" 
                       id="scheduled_date"
                       name="scheduled_date" 
                       value="{{ old('scheduled_date') }}"
                       min="{{ date('Y-m-d') }}"
                       required
                       class="{{ $errors->has('scheduled_date') ? 'error' : '' }}">
                @error('scheduled_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="notes">Notes (Optional)</label>
                <textarea id="notes"
                          name="notes" 
                          placeholder="Add any notes about the HomeCheck appointment (e.g., access instructions, special requirements, etc.)"
                          class="{{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-main" {{ $existingHomeCheck ? 'disabled' : '' }}>Schedule HomeCheck</button>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn" style="background: #666;">Cancel</a>
        </div>
    </form>
</div>
@endsection
