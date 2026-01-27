@extends('layouts.admin')

@section('title', 'Edit Email Template')

@section('content')
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
@endsection


