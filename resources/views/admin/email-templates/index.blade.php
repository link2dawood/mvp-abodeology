@extends('layouts.admin')

@section('title', 'Email Templates')

@push('styles')
<style>
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
        margin-bottom: 30px;
    }

    .card-body {
        padding: 0;
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

    /* TABLE */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .table thead {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border: none;
    }

    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
        vertical-align: middle;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background: #f9f9f9;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #666;
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
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

    .mt-3 {
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2>Email Templates</h2>
            <p class="page-subtitle">Manage reusable email templates for system notifications.</p>
        </div>
        <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">Create Template</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
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
                        <tr>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->action }}</td>
                            <td>{{ ucfirst($template->template_type) }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
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

            <div class="mt-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


