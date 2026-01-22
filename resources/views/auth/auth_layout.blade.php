<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Online Course Registration | Open University</title>

    <link rel="shortcut icon" href="{{ URL::to('mini.png') }}">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:; img-src 'self' data: https:; connect-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="{{ URL::to('assets/auth/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/auth/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/auth/css/style.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/auth/css/custom.css') }}">
</head>
<body>

<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    <img class="img-fluid" src="{{ URL::to('mini.png') }}" alt="Logo">
                    <h2 class="auth-left-title">Institute of Education and Management Technologies (IEMT)</h2>
                    <div class="auth-left-contact">
                        <p class="udsm-address">
                    <span class="contact">Contact</span><br>
                    P.O.BOX 23409, <br>
                    Kinondoni,Dar es Salaam, Tanzania<br>
                    <span class="telephone">+255 799 888 234 | +255 799 888 233</span><br>
			<span class="email">ict.training@out.ac.tz</span><br>
                </p>
                    </div>
                </div>
                <div class="login-right" style="background: #f9ffad;">
                    <div class="login-right-wrap">
                        @yield('h1')
                         @if(request()->routeIs('login'))
                        <p class="account-subtitle" style="color: black">New User? <a class="uni-blue" href="{{ route('register') }}">Register</a></p>
                        @endif
                        <h2>@yield('heading')</h2>
                        @yield('content')

                        <div class="login-or">
                            <span class="or-line"></span>
                            <span class="span-or">&nbsp;&nbsp;</span>
                        </div>
                        @stack('footer-links')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ URL::to('assets/auth/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::to('assets/auth/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::to('assets/auth/js/feather.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
@include('layouts.components.notify-js')

</body>
</html>
