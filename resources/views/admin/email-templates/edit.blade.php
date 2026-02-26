@extends('layouts.admin')

@section('title', 'Edit Email Template')

@section('styles')
    @include('admin.email-templates.partials.template-builder.styles')
@endsection

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
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .template-builder-page .btn {
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .template-builder-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 20px 30px;
        border-bottom: 1px solid #e0e0e0;
        z-index: 999;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        position: relative;
    }

    .template-builder-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #e0e0e0 50%, transparent);
    }

    .header-title-section {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .header-title-section h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #212529;
        letter-spacing: -0.3px;
    }

    .header-title-section .title-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--abodeology-teal), #1a9a96);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(44, 184, 180, 0.25);
    }

    .header-form-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .form-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-field-group label {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .form-field-group .form-control,
    .form-field-group .form-select {
        padding: 10px 14px;
        border: 1.5px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .form-field-group .form-control:focus,
    .form-field-group .form-select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
        background: #ffffff;
    }

    .form-field-group .form-control:hover,
    .form-field-group .form-select:hover {
        border-color: #c0c0c0;
    }

    .template-builder-footer {
        background: transparent;
        padding: 18px 30px;
        margin-bottom: 10px;
        border-top: 1px solid rgba(0,0,0,0.06);
        z-index: 1000;
        box-shadow: none;
        position: relative;
        flex-shrink: 0;
    }

    .template-builder-footer::before {
        display: none;
    }

    .footer-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 100%;
        margin: 0 auto;
    }

    .btn {
        padding: 10px 24px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .btn-primary:hover {
        background: #1a9a96;
        color: var(--white);
        box-shadow: 0 4px 8px rgba(44, 184, 180, 0.3);
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        background: #ffffff;
        color: #495057;
        border: 1.5px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .btn-outline-secondary:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-outline-primary {
        background: var(--abodeology-teal);
        color: var(--white);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .btn-outline-primary:hover {
        background: #1a9a96;
        color: var(--white);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(44, 184, 180, 0.3);
    }

    .btn-outline-danger {
        background: #ffffff;
        color: var(--danger);
        border: 1.5px solid var(--danger);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .btn-outline-danger:hover {
        background: rgba(225, 79, 79, 0.05);
        border-color: var(--danger);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(225, 79, 79, 0.15);
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }

    .form-check-input {
        margin-right: 8px;
        accent-color: var(--abodeology-teal);
    }

    .form-check.form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        border-radius: 2em;
    }

    @media (max-width: 1200px) {
        .header-form-section {
            grid-template-columns: 1fr;
        }

        .footer-actions {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }

        .action-buttons {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 768px) {
        .template-builder-header {
            padding: 15px 20px;
        }

        .template-builder-footer {
            padding: 15px 20px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
    <div class="template-builder-page">
    <div class="container">
        <div class="template-builder-header">
            <div class="header-title-section">
                <div class="title-icon"><i class="fa fa-envelope"></i></div>
                <h2>Edit Email Template</h2>
            </div>
            <div class="header-form-section">
                <div class="form-field-group">
                    <label for="name">Template Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $template->name) }}" placeholder="Enter template name" required>
                </div>
                <div class="form-field-group">
                    <label for="action">Email Action</label>
                    <select id="action" name="action" class="form-select" required>
                        @foreach($actions as $value => $label)
                            <option value="{{ $value }}" {{ old('action', $template->action) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field-group">
                    <label for="subject">Email Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" placeholder="Enter email subject" required>
                </div>
                <div class="form-field-group">
                    <label for="template_type">Template Type</label>
                    <select id="template_type" name="template_type" class="form-select" required>
                        <option value="override" {{ old('template_type', $template->template_type) === 'override' ? 'selected' : '' }}>Override default</option>
                        <option value="default" {{ old('template_type', $template->template_type) === 'default' ? 'selected' : '' }}>Use as default</option>
                    </select>
                </div>
                <div class="form-field-group form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}" style="padding: 0; margin: 0; margin-top: 20px; display: flex; flex-direction: column; height: calc(100vh - 150px);">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" id="name-hidden" value="{{ old('name', $template->name) }}">
            <input type="hidden" name="action" id="action-hidden" value="{{ old('action', $template->action) }}">
            <input type="hidden" name="subject" id="subject-hidden" value="{{ old('subject', $template->subject) }}">
            <input type="hidden" name="template_type" id="template_type-hidden" value="{{ old('template_type', $template->template_type) }}">
            <input type="hidden" name="is_active" id="is_active-hidden" value="{{ old('is_active', $template->is_active) ? '1' : '0' }}">

            @php($templateContext = $template)
            @include('admin.email-templates.partials.template-builder', ['template' => $templateContext])

            <div class="template-builder-footer">
                <div class="footer-actions">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" onclick="previewTemplate()">
                            <i class="fa fa-eye"></i> Test Preview
                        </button>
                        <a href="{{ route('admin.email-templates.destroy', $template->id) }}" class="btn btn-outline-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this template?')) { document.getElementById('form-delete-template').submit(); }">Delete</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <form id="form-delete-template" method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const actionSelect = document.getElementById('action');
            const subjectInput = document.getElementById('subject');
            const templateTypeSelect = document.getElementById('template_type');
            const isActiveInput = document.getElementById('is_active');
            const nameHidden = document.getElementById('name-hidden');
            const actionHidden = document.getElementById('action-hidden');
            const subjectHidden = document.getElementById('subject-hidden');
            const templateTypeHidden = document.getElementById('template_type-hidden');
            const isActiveHidden = document.getElementById('is_active-hidden');
            function syncInputs() {
                if (nameInput) nameHidden.value = nameInput.value;
                if (actionSelect) actionHidden.value = actionSelect.value;
                if (subjectInput) subjectHidden.value = subjectInput.value;
                if (templateTypeSelect) templateTypeHidden.value = templateTypeSelect.value;
                if (isActiveInput) isActiveHidden.value = isActiveInput.checked ? '1' : '0';
            }
            if (nameInput) nameInput.addEventListener('input', syncInputs);
            if (subjectInput) subjectInput.addEventListener('input', syncInputs);
            if (actionSelect) actionSelect.addEventListener('change', syncInputs);
            if (templateTypeSelect) templateTypeSelect.addEventListener('change', syncInputs);
            if (isActiveInput) isActiveInput.addEventListener('change', syncInputs);
        });
    </script>
@endsection
