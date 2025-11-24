@php
    $layout = 'layouts.app';
    if (in_array($user->role, ['admin', 'agent'])) {
        $layout = 'layouts.admin';
    } elseif ($user->role === 'seller' || ($user->role === 'both' && ($user->valuations()->exists() || $user->properties()->exists()))) {
        $layout = 'layouts.seller';
    } elseif ($user->role === 'buyer' || $user->role === 'both') {
        $layout = 'layouts.buyer';
    } elseif ($user->role === 'pva') {
        $layout = 'layouts.pva';
    }
@endphp
@extends($layout)

@section('title', 'My Profile')

@push('styles')
<style>
    .container {
        max-width: 800px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 28px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 20px;
        font-weight: 600;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--line-grey);
    }

    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--abodeology-teal);
    }

    .profile-info h3 {
        margin: 0 0 5px 0;
        font-size: 24px;
    }

    .profile-info p {
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
    }

    .info-value {
        color: var(--dark-text);
    }

    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 12px 20px;
        margin-bottom: 20px;
        color: #155724;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>My Profile</h2>
    <p class="page-subtitle">Manage your account information and security settings.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <!-- PROFILE HEADER -->
    <div class="card">
        <div class="profile-header">
            <img src="{{ $user->avatar_url }}" alt="Profile Avatar" class="avatar">
            <div class="profile-info">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
                <p style="text-transform: capitalize;">Role: {{ $user->role }}</p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('profile.edit') }}" class="btn">Edit Profile</a>
            <a href="{{ route('profile.edit') }}#password" class="btn btn-secondary">Change Password</a>
        </div>
    </div>

    <!-- PERSONAL INFORMATION -->
    <div class="card">
        <h3>Personal Information</h3>
        <div class="info-row">
            <span class="info-label">Full Name:</span>
            <span class="info-value">{{ $user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        @if($user->phone)
        <div class="info-row">
            <span class="info-label">Phone Number:</span>
            <span class="info-value">{{ $user->phone }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Role:</span>
            <span class="info-value" style="text-transform: capitalize;">{{ $user->role }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Account Created:</span>
            <span class="info-value">{{ $user->created_at->format('F j, Y') }}</span>
        </div>
        @if($user->email_verified_at)
        <div class="info-row">
            <span class="info-label">Email Verified:</span>
            <span class="info-value" style="color: #28a745;">✓ Verified on {{ $user->email_verified_at->format('F j, Y') }}</span>
        </div>
        @else
        <div class="info-row">
            <span class="info-label">Email Verified:</span>
            <span class="info-value" style="color: #dc3545;">✗ Not verified</span>
        </div>
        @endif
    </div>
</div>
@endsection

