@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="text-center mb-4">
    <h1 class="h2 mt-3" style="color: var(--text-primary);">Reset your password</h1>
    <p style="color: var(--text-secondary);">Enter your new password below to complete the reset process.</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('password.update') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ $email ?? old('email') }}" 
                       placeholder="your@email.com" autocomplete="email" readonly>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" placeholder="Your new password" autocomplete="new-password" autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="password_confirmation" 
                       placeholder="Confirm your new password" autocomplete="new-password">
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Reset password
                </button>
            </div>
        </form>
    </div>
</div>

<div class="text-center mt-3" style="color: var(--text-secondary);">
    Remember your password? 
    <a href="{{ route('login') }}" style="color: var(--text-primary);">Sign in</a>
</div>
@endsection
