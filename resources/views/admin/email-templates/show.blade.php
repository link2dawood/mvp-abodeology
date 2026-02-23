@extends('layouts.admin')

@section('title', 'View Email Template')

@section('content')
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
@endsection


