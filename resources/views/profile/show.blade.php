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
        background: var(--abodeology-teal);
    }

    .btn-secondary:hover {
        background: #25A29F;
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

    @media (max-width: 768px) {
        .container {
            padding: 0 12px;
            margin: 22px auto;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 6px;
        }

        .page-subtitle {
            margin-bottom: 18px;
            font-size: 14px;
        }

        .card {
            padding: 18px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 14px;
        }

        .profile-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 14px;
        }

        .avatar {
            width: 84px;
            height: 84px;
        }

        .profile-info h3 {
            font-size: 20px;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .profile-info p {
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding: 10px 0;
        }

        .info-label {
            font-size: 12px;
        }

        .info-value {
            width: 100%;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .action-buttons {
            flex-direction: column;
            gap: 8px;
            margin-top: 14px;
        }

        .action-buttons .btn {
            width: 100%;
            text-align: center;
            margin-top: 0;
            box-sizing: border-box;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 0 10px;
            margin: 16px auto;
        }

        h2 {
            font-size: 20px;
        }

        .card {
            padding: 14px;
        }

        .profile-info h3 {
            font-size: 18px;
        }

        .btn {
            padding: 10px 14px;
            font-size: 14px;
        }
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
                <p style="text-transform: capitalize;">
                    Role: {{ $user->role }}
                    @if($user->role === 'both')
                        <span style="background: linear-gradient(135deg, #FF8C00, #4CAF50); color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; margin-left: 8px; font-weight: 600;">
                            Dual-Role Client
                        </span>
                    @endif
                </p>
                @if($user->role === 'both')
                    <p style="color: #666; font-size: 13px; margin-top: 8px; font-style: italic;">
                        <strong>Dual-Role Client:</strong> You have access to both Buyer and Seller dashboards. Switch between them using the navigation menu.
                    </p>
                    <p style="color: #666; font-size: 13px; margin-top: 4px; font-style: italic;">
                        <strong>Shared AML Documents:</strong> AML documents are accessible across both roles - upload once and use for both buyer and seller activities.
                    </p>
                    <p style="color: #666; font-size: 13px; margin-top: 4px; font-style: italic;">
                        <strong>Separate Pipelines:</strong> Your buyer and seller pipelines remain permission-separated. Agents will view you as separate entries in each pipeline.
                    </p>
                @endif
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
    </div>

</div>
@endsection

