@extends('components.authentication')

@section('form')
    <h2 class="auth-title">Create new password</h2>
    <p class="auth-subtitle">
        Set a strong new password for your account to continue securely.
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-group">
            <label for="email" class="auth-label">Email Address</label>
            <input type="email"
                   name="email"
                   id="email"
                   class="auth-input @error('email') is-invalid @enderror"
                   value="{{ old('email', $request->email) }}"
                   placeholder="Enter your email address"
                   required
                   autocomplete="username">
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-group">
            <label for="password" class="auth-label">New Password</label>
            <div class="auth-input-wrap">
                <input type="password"
                       name="password"
                       id="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       placeholder="Enter new password"
                       required
                       autocomplete="new-password">
                <span class="auth-eye mdi mdi-eye-outline" id="togglePwd"></span>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-group">
            <label for="password_confirmation" class="auth-label">Confirm New Password</label>
            <div class="auth-input-wrap">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       class="auth-input @error('password_confirmation') is-invalid @enderror"
                       placeholder="Confirm new password"
                       required
                       autocomplete="new-password">
                <span class="auth-eye mdi mdi-eye-outline" id="togglePwd2"></span>
            </div>
            @error('password_confirmation')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="auth-btn">
            Reset Password
        </button>

        <div class="auth-note">
            <strong style="color:#0f172a;">Password tip</strong><br>
            Use a password that is difficult to guess and different from the one you used before.
        </div>

        <div style="text-align:center; margin-top:18px;">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="mdi mdi-arrow-left" style="font-size:.9rem;"></i>
                Back to sign in
            </a>
        </div>
    </form>

    <script>
        function togglePassword(toggleId, inputId) {
            document.getElementById(toggleId).addEventListener('click', function () {
                const input = document.getElementById(inputId);
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.classList.toggle('mdi-eye-outline', !isPassword);
                this.classList.toggle('mdi-eye-off-outline', isPassword);
            });
        }

        togglePassword('togglePwd', 'password');
        togglePassword('togglePwd2', 'password_confirmation');
    </script>
@endsection