@extends('layouts.admin')

@section('title', 'View Email Template')

@push('styles')
<style>
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
        color: var(--dark-text);
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .page-header {
        margin-bottom: 30px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
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

    .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--abodeology-teal);
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
        background: #1a9a96;
        color: var(--white);
    }

    .btn-outline-secondary {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-outline-secondary:hover {
        background: #f5f5f5;
    }

    .btn-outline-primary {
        background: var(--abodeology-teal);
        color: var(--white);
        border: none;
    }

    .btn-outline-primary:hover {
        background: #1a9a96;
        color: var(--white);
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge.bg-success {
        background: #d4edda;
        color: #155724;
    }

    .badge.bg-secondary {
        background: #e0e0e0;
        color: #666;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px;
        text-align: left;
        font-size: 14px;
        font-weight: 600;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:hover {
        background: #f9f9f9;
    }

    .table-sm th,
    .table-sm td {
        padding: 8px 12px;
        font-size: 13px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    .form-control.is-invalid {
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
        margin-right: 10px;
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--abodeology-teal);
    }

    .form-check.form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        border-radius: 2em;
    }

    .form-check-label {
        margin: 0;
        cursor: pointer;
        font-size: 14px;
    }

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
        }
    }

    .g-3 {
        gap: 1rem;
    }

    .align-items-end {
        align-items: flex-end;
    }

    .text-end {
        text-align: right;
    }

    .text-muted {
        color: #666;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .mb-4 {
        margin-bottom: 20px;
    }

    .mt-3 {
        margin-top: 15px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    dl.row {
        margin: 0;
    }

    dl.row dt {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    dl.row dd {
        margin-bottom: 15px;
        color: #666;
    }

    .border {
        border: 1px solid var(--line-grey) !important;
    }

    .rounded {
        border-radius: 6px;
    }

    .p-3 {
        padding: 15px;
    }

    small {
        font-size: 12px;
    }
</style>
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

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Email Template Preview</h5>
            <p class="text-muted mb-3">This preview shows how the email template will look with sample data.</p>
            
            <div class="border rounded p-3" style="background-color: #fff; max-width: 680px; margin: 0 auto;">
                {!! $renderedBody ?? $template->body !!}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Custom Preview</h5>
            <p class="text-muted mb-3">Provide custom sample data to preview variable resolution.</p>
            <form method="POST" action="{{ route('admin.email-templates.preview', $template->id) }}" target="_blank">
                @csrf
                <div class="mb-3">
                    <label for="preview-data" class="form-label">Sample Data (JSON)</label>
                    <textarea
                        id="preview-data"
                        name="data"
                        class="form-control"
                        rows="4"
                        placeholder='{"property":{"address":"123 Example Street"},"buyer":{"name":"John Doe"}}'
                    ></textarea>
                    <small class="text-muted">
                        Provide sample data in JSON format to preview variable resolution.
                    </small>
                </div>
                <button type="submit" class="btn btn-outline-primary">Open Custom Preview</button>
            </form>
        </div>
    </div>
    </div>
@endsection


