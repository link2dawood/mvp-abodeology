@extends('layouts.admin')

@section('title', 'AML Checks')

@push('styles')
<style>
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        margin-bottom: 30px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table th,
    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--line-grey);
        text-align: left;
        font-size: 14px;
    }

    .table th {
        background: #f5f5f5;
        font-weight: 600;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .status {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-verified {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .btn-main {
        background: var(--abodeology-teal);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
            white-space: nowrap;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>AML Document Checks</h2>
    <p class="page-subtitle">Review and verify Anti-Money Laundering documents uploaded by sellers.</p>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th>Checked By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($amlChecks as $check)
                    <tr>
                        <td>{{ $check->user->name ?? 'N/A' }}</td>
                        <td>{{ $check->user->email ?? 'N/A' }}</td>
                        <td>
                            <span class="status status-{{ $check->verification_status }}">
                                {{ ucfirst($check->verification_status) }}
                            </span>
                        </td>
                        <td>{{ $check->created_at->format('M j, Y') }}</td>
                        <td>
                            @if($check->checker)
                                {{ $check->checker->name }}<br>
                                <small style="color: #666;">{{ $check->checked_at ? $check->checked_at->format('M j, Y') : '' }}</small>
                            @else
                                <span style="color: #999;">Not checked</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.aml-checks.show', $check->id) }}" class="btn btn-main">View Documents</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            No AML checks found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($amlChecks->hasPages())
            <div style="margin-top: 20px;">
                {{ $amlChecks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

