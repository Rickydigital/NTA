@extends('components.authentication')

@section('form')
    <h2 class="auth-title">Confirm password</h2>
    <p class="auth-subtitle">
        For your security, please confirm your password before accessing this protected area.
    </p>

    <div class="auth-note" style="margin-top:0;margin-bottom:20px;">
        <strong style="color:#0f172a;">Secure verification</strong><br>
        This extra step helps protect student, parent, and staff information from unauthorized access.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-group">
            <label for="password" class="auth-label">Password</label>
            <div class="auth-input-wrap">
                <input type="password"
                       name="password"
                       id="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password"
                       autofocus>
                <span class="auth-eye mdi mdi-eye-outline" id="togglePwd"></span>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="auth-btn">
            Confirm & Continue
        </button>

        <div class="auth-note">
            <strong style="color:#0f172a;">Need help?</strong><br>
            If you cannot remember your password, return to the password recovery page and request a reset link.
        </div>
    </form>

    <script>
        document.getElementById('togglePwd').addEventListener('click', function () {
            const input = document.getElementById('password');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.classList.toggle('mdi-eye-outline', !isPassword);
            this.classList.toggle('mdi-eye-off-outline', isPassword);
        });
    </script>
@endsection