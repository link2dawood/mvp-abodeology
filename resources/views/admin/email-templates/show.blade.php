@extends('layouts.admin')

@section('title', 'View Email Template')

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
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 15px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
        color: var(--dark-text);
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 0;
        font-size: 14px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card-body {
        padding: 0;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--abodeology-teal);
        margin-bottom: 15px;
        margin-top: 0;
    }

    .mb-4 {
        margin-bottom: 20px;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb-3 {
        margin-bottom: 15px;
    }

    /* ALERT */
    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    /* DEFINITION LIST */
    dl.row {
        margin: 0;
    }

    dt.col-sm-3 {
        font-weight: 600;
        color: #333;
        padding: 8px 0;
    }

    dd.col-sm-9 {
        padding: 8px 0;
        color: #666;
    }

    /* TABLE */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .table-sm th,
    .table-sm td {
        padding: 8px 12px;
        font-size: 13px;
    }

    .table thead {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        border: none;
    }

    .table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 13px;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background: #f9f9f9;
    }

    /* BADGE */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .bg-success {
        background: #28a745;
        color: var(--white);
    }

    .bg-secondary {
        background: #6c757d;
        color: var(--white);
    }

    /* FORM */
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        display: block;
        color: var(--danger);
        font-size: 13px;
        margin-top: 5px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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

    /* GRID */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .col-md-3,
    .col-md-6 {
        padding: 0 10px;
        flex: 0 0 auto;
    }

    .col-md-3 {
        width: 25%;
    }

    .col-md-6 {
        width: 50%;
    }

    @media (max-width: 768px) {
        .col-md-3,
        .col-md-6 {
            width: 100%;
            margin-bottom: 15px;
        }
    }

    .g-3 {
        gap: 15px;
    }

    .align-items-end {
        align-items: flex-end;
    }

    .text-end {
        text-align: right;
    }

    .text-muted {
        color: #666;
        font-size: 13px;
    }

    small.text-muted {
        display: block;
        margin-top: 5px;
        font-size: 12px;
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

    .btn-outline-primary {
        background: transparent;
        color: var(--abodeology-teal);
        border: 1px solid var(--abodeology-teal);
    }

    .btn-outline-primary:hover {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    /* Live Preview Styles */
    .preview-container-live {
        background: var(--white);
        border: 2px solid var(--line-grey);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .preview-header-live {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid var(--line-grey);
    }

    .preview-subject-display-live {
        font-size: 14px;
        color: #333;
    }

    .preview-subject-display-live strong {
        color: var(--abodeology-teal);
    }

    .preview-controls-live {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .preview-refresh-btn-live {
        padding: 6px 12px;
        background: var(--abodeology-teal);
        color: var(--white);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .preview-refresh-btn-live:hover {
        background: #25A29F;
        transform: rotate(180deg);
    }

    .preview-device-select-live {
        padding: 6px 12px;
        border: 2px solid var(--line-grey);
        border-radius: 6px;
        font-size: 13px;
        background: var(--white);
        cursor: pointer;
    }

    .preview-device-select-live:focus {
        outline: none;
        border-color: var(--abodeology-teal);
    }

    .preview-frame-live {
        padding: 30px;
        background: #f4f4f4;
        min-height: 500px;
        max-height: 800px;
        overflow-y: auto;
        position: relative;
    }

    .preview-frame-live.desktop {
        max-width: 680px;
        margin: 0 auto;
    }

    .preview-frame-live.mobile {
        max-width: 420px;
        margin: 0 auto;
    }

    .preview-frame-live iframe {
        width: 100%;
        min-height: 500px;
        border: none;
        background: var(--white);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .preview-loading-live {
        text-align: center;
        padding: 50px;
        color: #666;
        font-size: 14px;
    }

    .preview-data-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--line-grey);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateBody = @json($template->body ?? '');
        const templateSubject = @json($template->subject ?? '');
        const previewFrame = document.getElementById('preview-frame-live');
        const previewSubject = document.getElementById('preview-subject-live');
        const previewDataTextarea = document.getElementById('preview-data-live');
        const refreshBtn = document.getElementById('refresh-preview-live');
        const deviceSelect = document.getElementById('preview-device-live');

        // Default sample data
        let sampleData = {
            property: {
                address: '123 Example Street',
                postcode: 'SW1A 1AA',
                asking_price: 450000
            },
            buyer: {
                name: 'John Doe',
                email: 'john.doe@example.com'
            },
            offer: {
                offer_amount: 425000,
                status: 'pending'
            },
            viewing: {
                viewing_date: '2024-01-15 14:00'
            },
            recipient: {
                name: 'Jane Smith'
            },
            user: {
                name: 'Jane Smith'
            },
            seller: {
                name: 'Jane Smith',
                email: 'jane.smith@example.com',
                phone: '+44 20 1234 5678'
            },
            status: 'sold',
            message: 'Your property has been successfully sold.'
        };

        // Replace variables in template
        function replaceVariables(template, data) {
            if (!template) return '';
            
            return template.replace(/\{\{([^}]+)\}\}/g, function(match, key) {
                key = key.trim();
                const keys = key.split('.');
                let value = data;
                
                for (let k of keys) {
                    if (value && typeof value === 'object' && k in value) {
                        value = value[k];
                    } else {
                        return match;
                    }
                }
                
                if (typeof value === 'number') {
                    if (key.includes('price') || key.includes('amount')) {
                        return '£' + value.toLocaleString('en-GB');
                    }
                    return value.toString();
                }
                
                return value || match;
            });
        }

        // Update preview
        function updatePreview() {
            try {
                // Try to parse JSON from textarea
                const jsonData = previewDataTextarea.value.trim();
                if (jsonData) {
                    sampleData = JSON.parse(jsonData);
                }
            } catch (e) {
                console.warn('Invalid JSON, using default data');
            }

            const renderedBody = replaceVariables(templateBody, sampleData);
            const renderedSubject = replaceVariables(templateSubject, sampleData);

            if (previewSubject) {
                previewSubject.textContent = renderedSubject || templateSubject;
            }

            if (previewFrame) {
                const device = deviceSelect ? deviceSelect.value : 'desktop';
                previewFrame.className = 'preview-frame-live ' + device;
                
                let iframe = previewFrame.querySelector('iframe');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    previewFrame.innerHTML = '';
                    previewFrame.appendChild(iframe);
                }

                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(renderedBody || '<p style="padding: 20px; color: #666;">No template body available.</p>');
                iframeDoc.close();
            }
        }

        // Event listeners
        if (previewDataTextarea) {
            previewDataTextarea.addEventListener('input', function() {
                updatePreview();
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                updatePreview();
            });
        }

        if (deviceSelect) {
            deviceSelect.addEventListener('change', function() {
                updatePreview();
            });
        }

        // Initial preview
        setTimeout(updatePreview, 300);
    });
</script>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Email Template: {{ $template->name }}</h2>
        <p class="page-subtitle">Review details, assignments, and preview the rendered content.</p>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">Back to list</a>
        <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-primary">Edit Template</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Template Details</h5>
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $template->name }}</dd>

                <dt class="col-sm-3">Action</dt>
                <dd class="col-sm-9">{{ $template->action }}</dd>

                <dt class="col-sm-3">Type</dt>
                <dd class="col-sm-9">{{ ucfirst($template->template_type) }}</dd>

                <dt class="col-sm-3">Active</dt>
                <dd class="col-sm-9">
                    @if($template->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Subject</dt>
                <dd class="col-sm-9">{{ $template->subject }}</dd>

                <dt class="col-sm-3">Created By</dt>
                <dd class="col-sm-9">{{ optional($template->creator)->name ?? 'System' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $template->created_at?->format('Y-m-d H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Assignments</h5>
            @if($template->assignments->isEmpty())
                <p class="text-muted mb-0">This template is not explicitly assigned to any action yet.</p>
            @else
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Active</th>
                            <th>Assigned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($template->assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->action }}</td>
                                <td>
                                    @if($assignment->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $assignment->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Assign Template to Action</h5>
            <form method="POST" action="{{ route('admin.email-templates.assign', $template->id) }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="assign-action" class="form-label">Action</label>
                        <input
                            type="text"
                            id="assign-action"
                            name="action"
                            class="form-control @error('action') is-invalid @enderror"
                            value="{{ old('action', $template->action) }}"
                            required
                        >
                        @error('action')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="assign-is-active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="assign-is-active">Set as active</label>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn btn-primary">Assign Template</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Live Preview</h5>
            <div class="preview-container-live">
                <div class="preview-header-live">
                    <div class="preview-subject-display-live">
                        <strong>Subject:</strong> <span id="preview-subject-live">{{ $template->subject }}</span>
                    </div>
                    <div class="preview-controls-live">
                        <button class="preview-refresh-btn-live" id="refresh-preview-live" title="Refresh Preview">Refresh</button>
                        <select id="preview-device-live" class="preview-device-select-live">
                            <option value="desktop">Desktop</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>
                </div>
                <div class="preview-frame-live" id="preview-frame-live">
                    <div class="preview-loading-live">Loading preview...</div>
                </div>
            </div>
            <div class="preview-data-section" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--line-grey);">
                <label for="preview-data-live" class="form-label">Sample Data (JSON) - Edit to update preview</label>
                <textarea
                    id="preview-data-live"
                    class="form-control"
                    rows="6"
                    placeholder='{"property":{"address":"123 Example Street","postcode":"SW1A 1AA","asking_price":450000},"buyer":{"name":"John Doe","email":"john.doe@example.com"},"offer":{"offer_amount":425000},"recipient":{"name":"Jane Smith"}}'
                >{"property":{"address":"123 Example Street","postcode":"SW1A 1AA","asking_price":450000},"buyer":{"name":"John Doe","email":"john.doe@example.com"},"offer":{"offer_amount":425000},"recipient":{"name":"Jane Smith"},"user":{"name":"Jane Smith"},"seller":{"name":"Jane Smith","email":"jane.smith@example.com","phone":"+44 20 1234 5678"},"status":"sold","message":"Your property has been successfully sold."}</textarea>
                <small class="text-muted">
                    Edit the JSON data above to see how variables are replaced in the preview. The preview updates automatically.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection


