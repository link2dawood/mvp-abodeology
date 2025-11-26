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

@section('title', 'Edit Profile')

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

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--dark-text);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 14px;
        border: 1px solid #D9D9D9;
        border-radius: 6px;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
    }

    input:focus {
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    input.error {
        border-color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
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
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #25A29F;
    }

    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--abodeology-teal);
        margin-bottom: 15px;
    }

    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
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
    <h2>Edit Profile</h2>
    <p class="page-subtitle">Update your personal information and account settings.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px;">
            <strong>Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- PROFILE INFORMATION -->
    <div class="card">
        <h3>Personal Information</h3>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" 
                       id="name"
                       name="name" 
                       value="{{ old('name', $user->name) }}"
                       required
                       class="{{ $errors->has('name') ? 'error' : '' }}">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email"
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       required
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            @if($user->phone)
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" 
                       id="phone"
                       name="phone" 
                       value="{{ old('phone', $user->phone) }}"
                       class="{{ $errors->has('phone') ? 'error' : '' }}">
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

    <!-- AVATAR -->
    <div class="card">
        <h3>Profile Picture</h3>
        
        @if(session('success') && str_contains(request()->url(), 'avatar'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->has('avatar') || (session('errors') && session('errors')->has('avatar')))
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
                <strong>Error:</strong>
                @if($errors->has('avatar'))
                    {{ $errors->first('avatar') }}
                @elseif(session('errors') && session('errors')->has('avatar'))
                    {{ session('errors')->first('avatar') }}
                @endif
            </div>
        @endif
        
        @if($user->avatar)
            <img src="{{ $user->avatar_url }}" alt="Profile Avatar" class="avatar-preview">
        @endif
        
        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="avatar">Upload New Avatar</label>
                <input type="file" 
                       id="avatar"
                       name="avatar" 
                       accept="image/jpeg,image/png,image/jpg,image/gif"
                       class="{{ $errors->has('avatar') ? 'error' : '' }}"
                       required>
                @error('avatar')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="help-text">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB. Dimensions: 50x50 to 2000x2000 pixels</div>
            </div>
            <button type="submit" class="btn">Update Avatar</button>
        </form>

        @if($user->avatar)
        <form action="{{ route('profile.avatar.remove') }}" method="POST" style="margin-top: 15px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to remove your avatar?')">Remove Avatar</button>
        </form>
        @endif
    </div>

    <!-- CHANGE PASSWORD -->
    <div class="card" id="password">
        <h3>Change Password</h3>
        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" 
                       id="current_password"
                       name="current_password" 
                       required
                       class="{{ $errors->has('current_password') ? 'error' : '' }}">
                @error('current_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" 
                       id="password"
                       name="password" 
                       required
                       class="{{ $errors->has('password') ? 'error' : '' }}">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="help-text">Password must be at least 8 characters long.</div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" 
                       id="password_confirmation"
                       name="password_confirmation" 
                       required>
            </div>

            <button type="submit" class="btn">Change Password</button>
        </form>
    </div>

    <!-- BACK BUTTON -->
    <div class="action-buttons">
        <a href="{{ route('profile.show') }}" class="btn btn-secondary">Back to Profile</a>
    </div>
</div>
@endsection

