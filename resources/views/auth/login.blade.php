@extends('components.authentication')

@section('form')
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">
        Sign in using your username or email address to continue to your school workspace and stay connected with updates for students, parents, and staff.
    </p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-group">
            <label for="login" class="auth-label">Username or Email Address</label>
            <input type="text"
                   name="login"
                   id="login"
                   class="auth-input @error('login') is-invalid @enderror"
                   value="{{ old('login') }}"
                   placeholder="Enter username or email address"
                   required
                   autocomplete="username"
                   autofocus>
            @error('login')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-group">
            <label for="password" class="auth-label">Password</label>
            <div class="auth-input-wrap">
                <input type="password"
                       name="password"
                       id="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password">
                <span class="auth-eye mdi mdi-eye-outline" id="togglePwd"></span>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-row">
            <label class="auth-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Keep me signed in
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="auth-btn">
            Sign In
        </button>

        <div class="auth-note">
            <strong style="color:#0f172a;">Login options</strong><br>
            You can sign in using either your assigned username or your email address together with your password.
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