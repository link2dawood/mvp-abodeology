@extends('layouts.admin')

@section('title', 'Users')

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

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
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

    .role-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .role-admin {
        background: #E14F4F;
        color: #FFF;
    }

    .role-agent {
        background: #6c757d;
        color: #FFF;
    }

    .role-buyer {
        background: var(--abodeology-teal);
        color: #FFF;
    }

    .role-seller {
        background: #28a745;
        color: #FFF;
    }

    .role-both {
        background: #F4C542;
        color: #000;
    }

    .role-pva {
        background: #17a2b8;
        color: #FFF;
    }

    .role-null {
        background: #e0e0e0;
        color: #666;
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

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        h2 {
            font-size: 24px;
        }

        .card {
            padding: 20px;
            overflow-x: auto;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            min-width: 600px;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
        }

        .role-badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .page-subtitle {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 20px;
        }

        .card {
            padding: 15px;
        }

        .table th,
        .table td {
            padding: 6px;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Users</h2>
            <p class="page-subtitle">View and manage all platform users (Admin Only)</p>
        </div>
    </div>

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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Email Verified</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="role-badge role-{{ $user->role ?? 'null' }}">
                                {{ $user->role ? ucfirst($user->role) : 'No Role' }}
                            </span>
                            @if($user->role === 'both')
                                <span style="font-size: 11px; color: #666; display: block; margin-top: 4px;">
                                    (Appears in both Buyer & Seller pipelines)
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($user->email_verified_at)
                                <span style="color: #28a745;">✓ Verified</span>
                            @else
                                <span style="color: #dc3545;">✗ Not Verified</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999; padding: 40px;">
                            No users found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div style="margin-top: 20px;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

