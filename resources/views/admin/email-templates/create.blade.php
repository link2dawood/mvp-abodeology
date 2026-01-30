@extends('layouts.admin')

@section('title', 'Create Email Template')

@push('styles')
<style>
    .template-builder-page .container {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    .template-builder-page .page-header {
        display: none;
    }

    .template-builder-page .card {
        border: none;
        box-shadow: none;
        margin: 0;
        padding: 0;
    }

    .template-builder-page .card-body {
        padding: 0;
    }

    .template-builder-page .form-control,
    .template-builder-page .form-select {
        border-radius: 4px;
    }

    .template-builder-page .btn {
        border-radius: 4px;
    }

    .template-builder-header {
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background: #ffffff;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        z-index: 999;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card-body {
        padding: 25px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-control-sm {
        padding: 6px 10px;
        font-size: 13px;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .form-check-input {
        margin-right: 8px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-check-label {
        margin: 0;
        cursor: pointer;
        font-size: 14px;
    }

    .btn {
        padding: 10px 20px;
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

    .btn-outline-secondary {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-outline-secondary:hover {
        background: #f5f5f5;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .mt-4 {
        margin-top: 20px;
    }

    .mb-2 {
        margin-bottom: 10px;
    }

    .d-flex {
        display: flex;
    }

    .me-2 {
        margin-right: 10px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .text-muted {
        color: #666;
    }

    code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-family: 'Courier New', monospace;
    }

    .template-builder-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .template-builder-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .variable-list {
        max-height: 260px;
        overflow: auto;
        border: 1px solid var(--line-grey);
        border-radius: 8px;
        padding: 10px 12px;
        background: #fafafa;
        font-size: 13px;
        margin-top: 10px;
    }

    .variable-list code {
        background: #eee;
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 12px;
    }

    .variable-list ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .variable-list li {
        margin: 5px 0;
    }

    .mb-1 {
        margin-bottom: 5px;
    }

    .mb-0 {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid template-builder-page">
        <div class="template-builder-header">
            <div style="max-width: 100%; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Create Email Template</h2>
                </div>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <label for="name" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 13px; color: #666;">Name:</span>
                            <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name') }}" placeholder="Template name" required style="width: 200px; display: inline-block;">
                        </label>
                        <label for="action" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 13px; color: #666;">Action:</span>
                            <select id="action" name="action" class="form-select form-select-sm" required style="width: 200px; display: inline-block;">
                                <option value="" disabled {{ old('action') ? '' : 'selected' }}>Select action...</option>
                                @foreach($actions as $value => $label)
                                    <option value="{{ $value }}" {{ old('action') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label for="subject" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 13px; color: #666;">Subject:</span>
                            <input type="text" id="subject" name="subject" class="form-control form-control-sm" value="{{ old('subject') }}" placeholder="Email subject" required style="width: 200px; display: inline-block;">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.email-templates.store') }}" style="padding-top: 0; padding-bottom: 70px;">
            @csrf
            <input type="hidden" name="name" id="name-hidden" value="{{ old('name') }}">
            <input type="hidden" name="action" id="action-hidden" value="{{ old('action') }}">
            <input type="hidden" name="subject" id="subject-hidden" value="{{ old('subject') }}">
            <input type="hidden" name="template_type" value="{{ old('template_type', 'override') }}">
            <input type="hidden" name="is_active" value="{{ old('is_active', true) ? '1' : '0' }}">

                @include('admin.email-templates.partials.template-builder')

            @include('admin.email-templates.partials.template-builder')

            <div style="position: fixed; bottom: 0; left: 0; right: 0; background: #ffffff; padding: 15px 20px; border-top: 1px solid #e0e0e0; z-index: 1000; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
                <div style="max-width: 100%; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Template</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Sync header inputs with hidden form fields
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const actionSelect = document.getElementById('action');
            const subjectInput = document.getElementById('subject');
            const nameHidden = document.getElementById('name-hidden');
            const actionHidden = document.getElementById('action-hidden');
            const subjectHidden = document.getElementById('subject-hidden');

            if (nameInput && nameHidden) {
                nameInput.addEventListener('input', function() {
                    nameHidden.value = this.value;
                });
            }
            if (actionSelect && actionHidden) {
                actionSelect.addEventListener('change', function() {
                    actionHidden.value = this.value;
                });
            }
            if (subjectInput && subjectHidden) {
                subjectInput.addEventListener('input', function() {
                    subjectHidden.value = this.value;
                });
            }
        });
    </script>
@endsection


