@extends('layouts.admin')

@section('title', 'Assign Viewing to PVA')

@push('styles')
<style>
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
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        max-width: 700px;
    }

    .viewing-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .viewing-info h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #333;
    }

    .info-row {
        display: flex;
        margin-bottom: 10px;
    }

    .info-label {
        font-weight: 600;
        width: 150px;
        color: #666;
    }

    .info-value {
        flex: 1;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-group select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
    }

    .form-group .error {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: #6c757d;
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-scheduled {
        background: #d1ecf1;
        color: #0c5460;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Assign Viewing to PVA</h2>
            <p class="page-subtitle">Assign this viewing job to a Property Viewing Assistant</p>
        </div>
    </div>

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="viewing-info">
            <h3>Viewing Details</h3>
            <div class="info-row">
                <div class="info-label">Property:</div>
                <div class="info-value">{{ $viewing->property->address ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Buyer:</div>
                <div class="info-value">{{ $viewing->buyer->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Viewing Date:</div>
                <div class="info-value">{{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $viewing->status }}">{{ ucfirst($viewing->status) }}</span>
                </div>
            </div>
            @if($viewing->pva)
                <div class="info-row">
                    <div class="info-label">Currently Assigned:</div>
                    <div class="info-value">{{ $viewing->pva->name }}</div>
                </div>
            @endif
        </div>

        <form action="{{ route('admin.viewings.assign.store', $viewing->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="pva_id">Select PVA *</label>
                <select name="pva_id" id="pva_id" required>
                    <option value="">-- Select a PVA --</option>
                    @foreach($pvas as $pva)
                        <option value="{{ $pva->id }}" {{ old('pva_id', $viewing->pva_id) == $pva->id ? 'selected' : '' }}>
                            {{ $pva->name }} ({{ $pva->email }})
                        </option>
                    @endforeach
                </select>
                @error('pva_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Assign Viewing</button>
                <a href="{{ route('admin.viewings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
