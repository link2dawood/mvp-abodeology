@extends('layouts.admin')

@section('title', 'Email Templates')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
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
        padding: 0;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--abodeology-teal);
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

    .table tr:last-child td {
        border-bottom: none;
    }

    .btn {
        padding: 8px 16px;
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

    .btn-outline-primary {
        background: transparent;
        color: var(--abodeology-teal);
        border: 1px solid var(--abodeology-teal);
    }

    .btn-outline-primary:hover {
        background: var(--abodeology-teal);
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-secondary {
        background: #e0e0e0;
        color: #666;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
        margin-left: 8px;
        font-size: 11px;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
        margin-left: 8px;
        font-size: 11px;
    }

    .text-muted {
        color: #666;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .mb-4 {
        margin-bottom: 20px;
    }

    .mb-1 {
        margin-bottom: 5px;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-3 {
        margin-top: 15px;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .align-items-center {
        align-items: center;
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

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-warning ul {
        margin: 10px 0 0 20px;
        padding: 0;
    }

    .alert-warning li {
        margin: 5px 0;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="page-header">
            <h2>Email Templates</h2>
            <p class="page-subtitle">Manage reusable email templates for system notifications.</p>
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">Create Template</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(!empty($activeTemplatesByAction))
            <div class="alert alert-warning">
                <strong>⚠️ Warning:</strong> Multiple active templates exist for the following actions:
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    @foreach($activeTemplatesByAction as $action => $count)
                        <li><strong>{{ $action }}:</strong> {{ $count }} active template(s). Only the latest one is being used.</li>
                    @endforeach
                </ul>
                <p style="margin: 10px 0 0 0; font-size: 13px;">
                    Consider deactivating older templates or using explicit assignments to avoid confusion.
                </p>
            </div>
        @endif

        {{-- Widgets Summary Card --}}
        <div class="card mb-4">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h5 class="card-title mb-1">Email Widgets</h5>
                    <p class="text-muted mb-0">
                        <strong>{{ $widgetsCount }}</strong> total widgets 
                        (<strong>{{ $activeWidgetsCount }}</strong> active)
                    </p>
                </div>
                <a href="{{ route('admin.email-templates.widgets') }}" class="btn btn-outline-primary">
                    View All Widgets
                </a>
            </div>
        </div>

        <div class="card">
        <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        @php
                            $hasMultipleActive = isset($activeTemplatesByAction[$template->action]) && $activeTemplatesByAction[$template->action] > 1;
                            $isCurrentlyUsed = isset($currentlyUsedTemplates[$template->action]) && $currentlyUsedTemplates[$template->action] == $template->id;
                        @endphp
                        <tr>
                            <td>
                                {{ $template->name }}
                                @if($hasMultipleActive && $template->is_active)
                                    <span class="badge badge-warning" title="Multiple active templates exist for this action. Only the latest one is being used.">
                                        ⚠️ Multiple Active
                                    </span>
                                @endif
                                @if($isCurrentlyUsed && $template->is_active)
                                    <span class="badge badge-info" title="This template is currently being used for this action">
                                        ✓ In Use
                                    </span>
                                @endif
                            </td>
                            <td>{{ $template->action }}</td>
                            <td>{{ ucfirst($template->template_type) }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ optional($template->creator)->name ?? 'System' }}</td>
                            <td>{{ $template->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No email templates found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 15px; padding: 0 25px 25px 25px;">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
@endsection


