@extends('layouts.admin')

@section('title', 'Edit HomeCheck Report')

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
    select,
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

    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 35px;
        line-height: 1.5;
        height: auto;
    }

    textarea {
        height: 120px;
        resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #2CB8B4;
        outline: none;
    }

    input.error,
    select.error,
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

    .btn-secondary {
        background: #666;
    }

    .info-box {
        background: #E8F4F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #1E1E1E;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row > div {
        flex: 1;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Edit HomeCheck Report</h2>
        <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">← Back to Property</a>
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
        @if($property->postcode)
            <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
        @endif
    </div>

    <form action="{{ route('admin.homechecks.update', $homecheckReport->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <h3>HomeCheck Details</h3>

            <div class="form-row">
                <div>
                    <label for="scheduled_date">Scheduled Date <span style="color: #dc3545;">*</span></label>
                    <input type="date" 
                           id="scheduled_date"
                           name="scheduled_date" 
                           value="{{ old('scheduled_date', $homecheckReport->scheduled_date ? $homecheckReport->scheduled_date->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d') }}"
                           class="{{ $errors->has('scheduled_date') ? 'error' : '' }}">
                    @error('scheduled_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="status">Status <span style="color: #dc3545;">*</span></label>
                    <select id="status"
                            name="status" 
                            required
                            class="{{ $errors->has('status') ? 'error' : '' }}">
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('status', $homecheckReport->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="scheduled" {{ old('status', $homecheckReport->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status', $homecheckReport->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $homecheckReport->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $homecheckReport->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label for="notes">Notes</label>
                <textarea id="notes"
                          name="notes" 
                          rows="4"
                          maxlength="1000"
                          placeholder="Add any notes about the HomeCheck (optional)"
                          class="{{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes', $homecheckReport->notes) }}</textarea>
                <small style="color: #666; font-size: 13px;">Maximum 1000 characters</small>
                @error('notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if($homecheckReport->scheduler)
            <div class="info-box" style="background: #f9f9f9;">
                <p style="margin: 0; font-size: 13px; color: #666;">
                    <strong>Scheduled by:</strong> {{ $homecheckReport->scheduler->name }} 
                    on {{ $homecheckReport->created_at->format('M d, Y g:i A') }}
                </p>
            </div>
        @endif

        @if($homecheckReport->completed_at)
            <div class="info-box" style="background: #d4edda;">
                <p style="margin: 0; font-size: 13px; color: #155724;">
                    <strong>Completed:</strong> 
                    @if($homecheckReport->completer)
                        by {{ $homecheckReport->completer->name }}
                    @endif
                    on {{ $homecheckReport->completed_at->format('M d, Y g:i A') }}
                </p>
            </div>
        @endif

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-main">
                Update HomeCheck
            </button>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
