@extends('layouts.admin')

@section('title', 'Create Email Template')

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
        background: linear-gradient(135deg, var(--abodeology-teal), #1a9a96);
        color: var(--white);
        box-shadow: 0 4px 12px rgba(44, 184, 180, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1a9a96, var(--abodeology-teal));
        box-shadow: 0 6px 16px rgba(44, 184, 180, 0.4);
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
        background: #ffffff;
        color: var(--abodeology-teal);
        border: 1.5px solid var(--abodeology-teal);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .btn-outline-primary:hover {
        background: rgba(44, 184, 180, 0.05);
        border-color: var(--abodeology-teal);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(44, 184, 180, 0.15);
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
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
                <h2>Create Email Template</h2>
            </div>
            <div class="header-form-section">
                <div class="form-field-group">
                    <label for="name">Template Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter template name" required>
                </div>
                <div class="form-field-group">
                    <label for="action">Email Action</label>
                    <select id="action" name="action" class="form-select" required>
                        <option value="" disabled {{ old('action') ? '' : 'selected' }}>Select an action...</option>
                        @foreach($actions as $value => $label)
                            <option value="{{ $value }}" {{ old('action') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field-group">
                    <label for="subject">Email Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Enter email subject" required>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.email-templates.store') }}" style="padding: 0; margin: 0; margin-top:20px; display: flex; flex-direction: column; height: calc(100vh - 150px);">
            @csrf
            <input type="hidden" name="name" id="name-hidden" value="{{ old('name') }}">
            <input type="hidden" name="action" id="action-hidden" value="{{ old('action') }}">
            <input type="hidden" name="subject" id="subject-hidden" value="{{ old('subject') }}">
            <input type="hidden" name="template_type" value="{{ old('template_type', 'override') }}">
            <input type="hidden" name="is_active" value="{{ old('is_active', true) ? '1' : '0' }}">

            @include('admin.email-templates.partials.template-builder')

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const actionSelect = document.getElementById('action');
                    const nameInput = document.getElementById('name');
                    const subjectInput = document.getElementById('subject');
                    const nameHidden = document.getElementById('name-hidden');
                    const actionHidden = document.getElementById('action-hidden');
                    const subjectHidden = document.getElementById('subject-hidden');
                    let loadedTemplate = null;
                    let availableWidgets = [];

                    // Sync visible inputs with hidden inputs
                    function syncInputs() {
                        if (nameInput) nameHidden.value = nameInput.value;
                        if (actionSelect) actionHidden.value = actionSelect.value;
                        if (subjectInput) subjectHidden.value = subjectInput.value;
                    }

                    if (nameInput) {
                        nameInput.addEventListener('input', syncInputs);
                    }
                    if (subjectInput) {
                        subjectInput.addEventListener('input', syncInputs);
                    }

                    // Load template when action is selected
                    if (actionSelect) {
                        actionSelect.addEventListener('change', function() {
                            syncInputs();
                            const selectedAction = this.value;
                            
                            if (!selectedAction) {
                                return;
                            }

                            // Show loading indicator
                            const editor = $('#template-body-editor');
                            if (editor.length) {
                                editor.summernote('code', '<p>Loading template...</p>');
                            }

                            // Fetch template for this action
                            fetch('{{ route("admin.email-templates.get-by-action") }}?action=' + encodeURIComponent(selectedAction), {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.template) {
                                    loadedTemplate = data.template;
                                    availableWidgets = data.widgets || [];
                                    
                                    // Update logo URL if provided
                                    if (data.logoUrl && window.updateLogoUrl) {
                                        window.updateLogoUrl(data.logoUrl);
                                    }
                                    
                                    // Populate form fields
                                    if (nameInput && !nameInput.value) {
                                        nameInput.value = data.template.name;
                                        nameHidden.value = data.template.name;
                                    }
                                    if (subjectInput && !subjectInput.value) {
                                        subjectInput.value = data.template.subject;
                                        subjectHidden.value = data.template.subject;
                                    }

                                    // Load template body into editor
                                    if (editor.length && window.parseTemplateToWidgets) {
                                        window.parseTemplateToWidgets(data.template.body, availableWidgets);
                                    } else if (editor.length) {
                                        editor.summernote('code', data.template.body);
                                    }
                                } else {
                                    // No template found, clear editor
                                    if (editor.length) {
                                        editor.summernote('code', '');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error loading template:', error);
                                if (editor.length) {
                                    editor.summernote('code', '');
                                }
                            });
                        });
                    }
                });
            </script>
            @endpush

            <div class="template-builder-footer">
                <div class="footer-actions">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" onclick="previewTemplate()">
                            <i class="fa fa-eye"></i> Test Preview
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Template
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
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


