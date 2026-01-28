@extends('layouts.admin')

@section('title', 'Create Email Template')

@push('styles')
<style>
    /* CSS VARIABLES */
    :root {
        --abodeology-teal: #2CB8B4;
        --black: #0F0F0F;
        --white: #FFFFFF;
        --soft-grey: #F4F4F4;
        --dark-text: #1E1E1E;
        --line-grey: #EAEAEA;
        --danger: #E14F4F;
    }

    .container {
        max-width: 1400px;
        margin: 35px auto;
        padding: 0 22px;
    }

    /* PAGE HEADER */
    .page-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--line-grey);
    }

    .page-header h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--dark-text);
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 20px;
        font-size: 15px;
        line-height: 1.6;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .card-body {
        padding: 0;
    }

    /* FORM SECTION DIVIDER */
    .form-section {
        padding: 20px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--abodeology-teal);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(44, 184, 180, 0.2);
    }

    /* FORM STYLING */
    .mb-3 {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        letter-spacing: 0.2px;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--line-grey);
        border-radius: 8px;
        font-size: 15px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        transition: all 0.3s ease;
        background: var(--white);
        color: var(--dark-text);
    }

    .form-control:hover,
    .form-select:hover {
        border-color: #d0d0d0;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 4px rgba(44, 184, 180, 0.15);
        background: #fafafa;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: var(--danger);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(225, 79, 79, 0.15);
    }

    .invalid-feedback {
        display: block;
        color: var(--danger);
        font-size: 13px;
        margin-top: 8px;
        font-weight: 500;
    }

    /* FORM CHECK SWITCH */
    .form-check {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f9f9f9;
        border-radius: 8px;
        border: 1px solid var(--line-grey);
    }

    .form-check-input {
        width: 52px;
        height: 28px;
        cursor: pointer;
        appearance: none;
        background-color: #ccc;
        border-radius: 28px;
        position: relative;
        transition: all 0.3s ease;
        flex-shrink: 0;
        border: 2px solid transparent;
    }

    .form-check-input:hover {
        background-color: #b8b8b8;
    }

    .form-check-input:checked {
        background-color: var(--abodeology-teal);
        border-color: var(--abodeology-teal);
    }

    .form-check-input:checked:hover {
        background-color: #25A29F;
    }

    .form-check-input::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--white);
        top: 1px;
        left: 1px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .form-check-input:checked::before {
        transform: translateX(24px);
    }

    .form-check-label {
        font-size: 15px;
        color: #333;
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }

    /* BUTTONS */
    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        letter-spacing: 0.3px;
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
        border-color: var(--abodeology-teal);
        box-shadow: 0 4px 12px rgba(44, 184, 180, 0.3);
    }

    .btn-primary:hover {
        background: #25A29F;
        border-color: #25A29F;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(44, 184, 180, 0.4);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        color: var(--white);
        border-color: #6c757d;
    }

    .mt-4 {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid var(--line-grey);
    }

    /* TEMPLATE BUILDER GRID */
    .template-builder-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
        gap: 30px;
        margin-top: 20px;
    }

    @media (max-width: 992px) {
        .template-builder-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* VARIABLE HELPER */
    .variable-helper-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px solid var(--line-grey);
        border-radius: 10px;
        padding: 20px;
    }

    .variable-helper-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--abodeology-teal);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .variable-helper-title::before {
        content: '';
        font-size: 20px;
    }

    .variable-input-group {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }

    .variable-input-group input {
        flex: 1;
        padding: 10px 14px;
        border: 2px solid var(--line-grey);
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Courier New', monospace;
    }

    .variable-input-group input:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    .variable-list {
        max-height: 320px;
        overflow-y: auto;
        border: 2px solid var(--line-grey);
        border-radius: 8px;
        padding: 16px;
        background: var(--white);
        font-size: 13px;
        margin-top: 15px;
    }

    .variable-list::-webkit-scrollbar {
        width: 8px;
    }

    .variable-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .variable-list::-webkit-scrollbar-thumb {
        background: var(--abodeology-teal);
        border-radius: 4px;
    }

    .variable-list::-webkit-scrollbar-thumb:hover {
        background: #25A29F;
    }

    .variable-list strong {
        display: block;
        color: var(--dark-text);
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .variable-list ul {
        margin: 8px 0;
        padding-left: 22px;
        list-style: none;
    }

    .variable-list ul li {
        margin-bottom: 8px;
        padding-left: 8px;
        position: relative;
        line-height: 1.6;
    }

    .variable-list ul li::before {
        content: '→';
        position: absolute;
        left: -12px;
        color: var(--abodeology-teal);
        font-weight: bold;
    }

    .variable-list code {
        background: linear-gradient(135deg, #e8f4f3 0%, #f0f9f8 100%);
        padding: 4px 8px;
        border-radius: 5px;
        font-size: 12px;
        font-family: 'Courier New', monospace;
        color: var(--abodeology-teal);
        font-weight: 600;
        border: 1px solid rgba(44, 184, 180, 0.2);
    }

    .text-muted {
        color: #666;
        font-size: 12px;
        line-height: 1.5;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--line-grey);
    }

    .d-flex {
        display: flex;
    }

    .me-2 {
        margin-right: 8px;
    }

    .btn-sm {
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 6px;
        background: var(--abodeology-teal);
        color: var(--white);
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-sm:hover {
        background: #25A29F;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(44, 184, 180, 0.3);
    }

    /* SUMMERNOTE EDITOR STYLING */
    .note-editor {
        border: 2px solid var(--line-grey);
        border-radius: 8px;
        overflow: hidden;
    }

    .note-editor:focus-within {
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 4px rgba(44, 184, 180, 0.15);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .container {
            padding: 0 15px;
        }

        .card {
            padding: 20px;
        }

        .page-header h2 {
            font-size: 26px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Create Email Template</h2>
        <p class="page-subtitle">Define a reusable email template for a specific system action. Customize the subject and body content with dynamic variables.</p>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">← Back to list</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.email-templates.store') }}">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">Basic Information</div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Template Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="e.g., New Offer Notification - Premium"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="action" class="form-label">Email Action</label>
                        <select
                            id="action"
                            name="action"
                            class="form-select @error('action') is-invalid @enderror"
                            required
                        >
                            <option value="" disabled {{ old('action') ? '' : 'selected' }}>Select action...</option>
                            @foreach($actions as $value => $label)
                                <option value="{{ $value }}" {{ old('action') === $value ? 'selected' : '' }}>
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
                            value="{{ old('key') }}"
                            placeholder="e.g., seller-new-offer (auto-generated from action)"
                        >
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" style="display: block; margin-top: 6px;">Unique identifier for this template. Leave empty to auto-generate from action.</small>
                    </div>

                    @push('scripts')
                    <script>
                        // Auto-generate key from action
                        document.getElementById('action').addEventListener('change', function() {
                            const keyInput = document.getElementById('key');
                            if (!keyInput.value) {
                                const action = this.value;
                                const generatedKey = action.replace(/_/g, '-');
                                keyInput.value = generatedKey;
                            }
                        });
                    </script>
                    @endpush

                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            class="form-control @error('subject') is-invalid @enderror"
                            value="{{ old('subject') }}"
                            placeholder="e.g., New Offer Received - {{ '{' }}{{ 'property.address' }}{{ '}' }}"
                            required
                        >
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" style="display: block; margin-top: 6px;">You can use variables like {{ '{' }}{{ 'property.address' }}{{ '}' }} in the subject line.</small>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Template Settings</div>
                    
                    <div class="mb-3">
                        <label for="template_type" class="form-label">Template Type</label>
                        <select
                            id="template_type"
                            name="template_type"
                            class="form-select @error('template_type') is-invalid @enderror"
                            required
                        >
                            <option value="override" {{ old('template_type', 'override') === 'override' ? 'selected' : '' }}>Override default template</option>
                            <option value="default" {{ old('template_type') === 'default' ? 'selected' : '' }}>Use as default template</option>
                        </select>
                        @error('template_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" style="display: block; margin-top: 6px;">Override replaces the default view, while default is used as fallback.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="is_active"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_active">Activate this template</label>
                    </div>
                </div>

                <div class="form-section">
                    @include('admin.email-templates.partials.template-builder')
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Template</button>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary" style="margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


