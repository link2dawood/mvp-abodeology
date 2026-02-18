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
        box-sizing: border-box;
        max-width: 100%;
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

    .aml-desktop {
        display: block;
    }

    .aml-mobile {
        display: none;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 12px;
        }

        .page-subtitle {
            font-size: 14px;
            margin-bottom: 14px;
        }

        .card {
            padding: 18px;
            overflow: hidden;
        }

        .aml-desktop {
            display: none;
        }

        .aml-mobile {
            display: block;
        }

        .aml-mobile-card {
            border: 1px solid var(--line-grey);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .aml-mobile-name {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .aml-mobile-email {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 8px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .aml-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-top: 1px solid #f2f2f2;
        }

        .aml-mobile-label {
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            flex: 0 0 38%;
        }

        .aml-mobile-value {
            color: #1f2937;
            font-size: 13px;
            text-align: right;
            flex: 1;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .aml-mobile-actions {
            margin-top: 10px;
        }

        .aml-mobile-actions .btn {
            width: 100%;
            text-align: center;
            margin: 0;
            margin-right: 0;
            box-sizing: border-box;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
            white-space: normal;
        }

        .card .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 8px;
        }

        h2 {
            font-size: 20px;
        }

        .card {
            padding: 14px;
        }

        .aml-mobile-name {
            font-size: 14px;
        }

        .aml-mobile-label,
        .aml-mobile-value {
            font-size: 12px;
        }

        .status {
            font-size: 11px;
            padding: 5px 9px;
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
        <div class="aml-desktop">
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
        </div>

        <div class="aml-mobile">
            @forelse($amlChecks as $check)
                <div class="aml-mobile-card">
                    <div class="aml-mobile-name">{{ $check->user->name ?? 'N/A' }}</div>
                    <div class="aml-mobile-email">{{ $check->user->email ?? 'N/A' }}</div>

                    <div class="aml-mobile-row">
                        <div class="aml-mobile-label">Status</div>
                        <div class="aml-mobile-value">
                            <span class="status status-{{ $check->verification_status }}">
                                {{ ucfirst($check->verification_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="aml-mobile-row">
                        <div class="aml-mobile-label">Uploaded</div>
                        <div class="aml-mobile-value">{{ $check->created_at->format('M j, Y') }}</div>
                    </div>
                    <div class="aml-mobile-row">
                        <div class="aml-mobile-label">Checked By</div>
                        <div class="aml-mobile-value">
                            @if($check->checker)
                                {{ $check->checker->name }}<br>
                                <small style="color: #666;">{{ $check->checked_at ? $check->checked_at->format('M j, Y') : '' }}</small>
                            @else
                                <span style="color: #999;">Not checked</span>
                            @endif
                        </div>
                    </div>

                    <div class="aml-mobile-actions">
                        <a href="{{ route('admin.aml-checks.show', $check->id) }}" class="btn btn-main">View Documents</a>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: #666;">No AML checks found.</div>
            @endforelse
        </div>

        @if($amlChecks->hasPages())
            <div style="margin-top: 20px;">
                {{ $amlChecks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

