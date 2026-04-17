@extends('components.authentication')

@section('form')
    <h2 class="auth-title">Verify your email</h2>
    <p class="auth-subtitle">
        Before continuing, please verify your email address to activate access to your school portal.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-alert auth-alert-success">
            A fresh verification link has been sent to your email address.
        </div>
    @endif

    <div class="auth-note" style="margin-top:0;margin-bottom:20px;">
        <strong style="color:#0f172a;">Almost done</strong><br>
        Open your inbox and click the verification link we sent you. If you did not receive it, you can request another one below.
    </div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="auth-btn">
            Resend Verification Email
        </button>
    </form>

    <div style="margin-top:16px;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="auth-btn"
                    style="background:linear-gradient(135deg,#475569,#64748b); box-shadow:0 12px 24px rgba(71,85,105,.18);">
                Sign Out
            </button>
        </form>
    </div>
@endsection