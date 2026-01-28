@extends('layouts.admin')

@section('title', 'Edit Email Template')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 35px auto;
        padding: 0 22px;
    }

    /* PAGE HEADER */
    .page-header {
        margin-bottom: 30px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
        color: var(--dark-text);
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
        font-size: 14px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .card-body {
        padding: 0;
    }

    /* FORM STYLING */
    .mb-3 {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background: var(--white);
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        display: block;
        color: var(--danger);
        font-size: 13px;
        margin-top: 5px;
    }

    /* FORM CHECK SWITCH */
    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-check-input {
        width: 48px;
        height: 24px;
        cursor: pointer;
        appearance: none;
        background-color: #ccc;
        border-radius: 24px;
        position: relative;
        transition: background-color 0.3s ease;
        flex-shrink: 0;
    }

    .form-check-input:checked {
        background-color: var(--abodeology-teal);
    }

    .form-check-input::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--white);
        top: 2px;
        left: 2px;
        transition: transform 0.3s ease;
    }

    .form-check-input:checked::before {
        transform: translateX(24px);
    }

    .form-check-label {
        font-size: 14px;
        color: #333;
        cursor: pointer;
        margin: 0;
    }

    /* BUTTONS */
    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #25A29F;
        transform: translateY(-1px);
        box-shadow: 0px 4px 12px rgba(44, 184, 180, 0.3);
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6c757d;
        border: 1px solid #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        color: var(--white);
    }

    .btn-outline-danger {
        background: transparent;
        color: var(--danger);
        border: 1px solid var(--danger);
    }

    .btn-outline-danger:hover {
        background: var(--danger);
        color: var(--white);
    }

    .mt-4 {
        margin-top: 25px;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Edit Email Template</h2>
        <p class="page-subtitle">Update the content and settings for this email template.</p>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $template->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="action" class="form-label">Action</label>
                    <select
                        id="action"
                        name="action"
                        class="form-select @error('action') is-invalid @enderror"
                        required
                    >
                        @foreach($actions as $value => $label)
                            <option value="{{ $value }}" {{ old('action', $template->action) === $value ? 'selected' : '' }}>
                                {{ $label }} ({{ $value }})
                            </option>
                        @endforeach
                    </select>
                    @error('action')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="key" class="form-label">Template Key</label>
                    <input
                        type="text"
                        id="key"
                        name="key"
                        class="form-control @error('key') is-invalid @enderror"
                        value="{{ old('key', $template->key) }}"
                        placeholder="e.g., seller-new-offer"
                    >
                    @error('key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted" style="display: block; margin-top: 6px;">Unique identifier for this template.</small>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        class="form-control @error('subject') is-invalid @enderror"
                        value="{{ old('subject', $template->subject) }}"
                        required
                    >
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="template_type" class="form-label">Template Type</label>
                    <select
                        id="template_type"
                        name="template_type"
                        class="form-select @error('template_type') is-invalid @enderror"
                        required
                    >
                        <option value="override" {{ old('template_type', $template->template_type) === 'override' ? 'selected' : '' }}>Override default</option>
                        <option value="default" {{ old('template_type', $template->template_type) === 'default' ? 'selected' : '' }}>Use as default</option>
                    </select>
                    @error('template_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                @php($templateContext = $template)
                @include('admin.email-templates.partials.template-builder', ['template' => $templateContext])

                <div class="mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Save Changes</button>

                    <form method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" onsubmit="return confirm('Are you sure you want to delete this template?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Delete Template</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


