@extends('auth.auth_layout')
@section('heading')
    Login
@endsection
@section('h1')
     <img class="only-small-screen" src="{{ URL::to('mini.png') }}" alt="Logo">
    <h1 class="text-welcome-title" style="color: #013ba6; font-size: 20px; font-weight: 700; margin-bottom: 20px; text-align: center;">WELCOME TO SHORT COURSE MANAGEMENT SYSTEM (SCMS)</h1>
@endsection
@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label>{{ __('Email') }} <span class="login-danger">*</span></label>
            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
            <span class="profile-views"><i class="fas fa-user"></i></span>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label>{{ __('Password') }} <span class="login-danger">*</span></label>
            <input id="password" type="password" class="form-control pass-input @error('password') is-invalid @enderror"
                name="password" required autocomplete="current-password">
            <span class="profile-views feather-eye toggle-password"></span>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="forgotpass">
            <div class="remember-me">
                <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> {{ __('Remember Me') }}
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="checkmark"></span>
                </label>
            </div>
            @if (Route::has('password.request'))
                <a class="uni-blue" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" type="submit">{{ __('Login') }}</button>
        </div>
        @if (app()->environment('local'))
            <div class="mt-3 p-2 border rounded bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted font-weight-bold">Dev Quick Login (Local Only)</small>
                    <label class="mb-0 small text-muted" style="cursor: pointer;">
                        <input type="checkbox" id="quick-login-autosubmit" checked> Auto-submit
                    </label>
                </div>
                <div class="d-flex flex-column" style="gap: 6px;">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-block text-left"
                        onclick="quickLogin('admin@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-user-shield mr-1"></i> Administrator <small class="text-muted">(admin@scms.out.co.tz)</small>
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm btn-block text-left"
                        onclick="quickLogin('branch@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-building mr-1"></i> Branch Manager <small class="text-muted">(branch@scms.out.co.tz)</small>
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm btn-block text-left"
                        onclick="quickLogin('student@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-user-graduate mr-1"></i> Student <small class="text-muted">(student@scms.out.co.tz)</small>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-block text-left"
                        onclick="quickLogin('director@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-user-tie mr-1"></i> Director <small class="text-muted">(director@scms.out.co.tz)</small>
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm btn-block text-left"
                        onclick="quickLogin('marketing@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-bullhorn mr-1"></i> Marketing Officer <small class="text-muted">(marketing@scms.out.co.tz)</small>
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm btn-block text-left"
                        onclick="quickLogin('tutor@scms.out.co.tz', 'Password123456789@')">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Instructor <small class="text-muted">(tutor@scms.out.co.tz)</small>
                    </button>
                </div>
            </div>
            <script>
                function quickLogin(email, password) {
                    var emailInput = document.getElementById('email');
                    var passwordInput = document.getElementById('password');
                    var autoSubmit = document.getElementById('quick-login-autosubmit');
                    if (emailInput && passwordInput) {
                        emailInput.value = email;
                        passwordInput.value = password;
                        if ((!autoSubmit || autoSubmit.checked) && emailInput.form) {
                            emailInput.form.submit();
                        }
                    }
                }
            </script>
        @endif
    </form>
@endsection
