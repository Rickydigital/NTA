@extends('components.authentication')

@section('form')
    <h2 class="auth-title">Forgot password?</h2>
    <p class="auth-subtitle">
        Enter your email address and we will send you a secure link to reset your password and regain access to your account.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-group">
            <label for="email" class="auth-label">Email Address</label>
            <input type="email"
                   name="email"
                   id="email"
                   class="auth-input @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Enter your email address"
                   required
                   autofocus
                   autocomplete="email">
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="auth-btn">
            Send Reset Link
        </button>

        <div class="auth-note">
            <strong style="color:#0f172a;">Check your inbox</strong><br>
            After submission, look for a password reset email. If you do not see it, also check your spam folder.
        </div>

        <div style="text-align:center; margin-top:18px;">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="mdi mdi-arrow-left" style="font-size:.9rem;"></i>
                Back to sign in
            </a>
        </div>
    </form>
@endsection