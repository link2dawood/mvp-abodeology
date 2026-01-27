@extends('layouts.admin')

@section('title', 'Preview: ' . $template->name)

@push('styles')
    <style>
        .preview-device-toggle button {
            margin-right: 8px;
        }

        .preview-frame-desktop {
            max-width: 680px;
        }

        .preview-frame-mobile {
            max-width: 420px;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2>Preview: {{ $template->name }}</h2>
        <p class="page-subtitle">Rendered output using the provided sample data.</p>
        <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-outline-secondary">Back to template</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Subject &amp; Body</h5>
                <div class="preview-device-toggle">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-mode="desktop">Desktop</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-mode="mobile">Mobile</button>
                </div>
            </div>

            <p><strong>Subject:</strong> {{ $template->subject }}</p>

            <div id="preview-frame" class="border rounded p-3 preview-frame-desktop" style="background-color: #fff; margin-top: 10px;">
                {!! $renderedBody !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const frame = document.getElementById('preview-frame');
            const buttons = document.querySelectorAll('[data-preview-mode]');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const mode = this.getAttribute('data-preview-mode');
                    frame.classList.toggle('preview-frame-desktop', mode === 'desktop');
                    frame.classList.toggle('preview-frame-mobile', mode === 'mobile');
                });
            });
        });
    </script>
@endpush


