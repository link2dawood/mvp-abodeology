@extends('layouts.admin')

@section('title', 'Email Templates')

@section('content')
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
@endsection


