@extends('auth.auth_layout')
@section('heading')
<small> Update Password
</small>

@endsection
@section('h1')
    <h1 class="text-welcome text-dark text-center">SHORTCOURSE MANAGEMENT SYSTEM [SMS]</h1>
@endsection

@section('content')
@if ($error_msg)
<div class="text-danger text-small">
    {{ $error_msg }}
</div>
    
@endif
                    <form method="POST" action="{{ route('password.force_reset.update') }} " class="mt-3">
                        @csrf
                        <div class="form-group">
                            <label>New Password<span class="login-danger">*</span></label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required  autofocus>                                <span class="profile-views"><i class="fas fa-lock"></i></span>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm Password<span class="login-danger">*</span></label>
                            <input id="password-confirm" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" value="{{ old('password') }}" required  autofocus>                                <span class="profile-views"><i class="fas fa-lock"></i></span>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
@endsection
