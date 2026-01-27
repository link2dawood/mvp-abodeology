@extends('layouts.admin')

@section('title', 'Create Email Template')

@section('content')
    <div class="page-header">
        <h2>Create Email Template</h2>
        <p class="page-subtitle">Define a reusable email template for a specific system action.</p>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.email-templates.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
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
                    <label for="subject" class="form-label">Subject</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        class="form-control @error('subject') is-invalid @enderror"
                        value="{{ old('subject') }}"
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
                        <option value="override" {{ old('template_type', 'override') === 'override' ? 'selected' : '' }}>Override default</option>
                        <option value="default" {{ old('template_type') === 'default' ? 'selected' : '' }}>Use as default</option>
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
                        {{ old('is_active', true) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                @include('admin.email-templates.partials.template-builder')

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Template</button>
                </div>
            </form>
        </div>
    </div>
@endsection


