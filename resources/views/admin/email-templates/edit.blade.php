@extends('layouts.admin')

@section('title', 'Edit Email Template')

@push('styles')
<style>
    .main-content .container { max-width: 1180px; margin: 35px auto; padding: 0 22px; }
    h2 { font-size: 28px; margin-bottom: 8px; color: var(--dark-text); }
    .page-header { margin-bottom: 30px; display: flex; flex-wrap: wrap; align-items: center; gap: 15px; }
    .page-subtitle { color: #666; margin-bottom: 0; flex-basis: 100%; font-size: 15px; }
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .card-body { padding: 0; }
    .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px; }
    .form-control, .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #dc3545; }
    .invalid-feedback { display: block; color: #dc3545; font-size: 13px; margin-top: 5px; }
    .form-check { display: flex; align-items: center; margin-bottom: 20px; }
    .form-check-input { margin-right: 10px; width: 20px; height: 20px; cursor: pointer; accent-color: var(--abodeology-teal); }
    .form-check.form-switch .form-check-input { width: 2.5em; height: 1.25em; border-radius: 2em; }
    .form-check-label { margin: 0; cursor: pointer; font-size: 14px; }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .btn-primary { background: var(--abodeology-teal); color: var(--white); }
    .btn-primary:hover { background: #25A29F; color: var(--white); }
    .btn-outline-secondary { background: transparent; color: #666; border-color: #ddd; }
    .btn-outline-secondary:hover { background: #f5f5f5; color: #333; }
    .btn-outline-danger { background: transparent; color: var(--danger); border-color: var(--danger); }
    .btn-outline-danger:hover { background: var(--danger); color: var(--white); }
    .mb-3 { margin-bottom: 20px; }
    .mt-4 { margin-top: 24px; }
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
</style>
@endpush

@section('styles')
    @include('admin.email-templates.partials.template-builder.styles')
@endsection

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


