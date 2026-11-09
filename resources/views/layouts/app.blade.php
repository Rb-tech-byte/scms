<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:; img-src 'self' data: https:; connect-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;">
    @csrf
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>@yield('title', $title ?? 'Scms') </title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('mini.png') }}">
    <link rel="shortcut icon" href="{{ URL::to('mini.png') }}">
    <link rel="icon" type="image/icon" href="{{ asset('mini.png') }}" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.3/sweetalert2.min.css"  />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js"></script>

    @stack('css')
    <style>
        .square-radio .form-check-input {
            width: 20px;
            height: 20px;
            border-radius: 0;
        }


        .square-radio .form-check-input:checked {
            background-color: #013ba6;
            border-color: #013ba6;
        }

        .none {
            display: none;
        }

        :root {
            --brand-navy: #013ba6;
            --brand-blue: #228ddd;
            --brand-yellow: #f4ed2d;
        }

        .open-blue-mode {
            background-color: #013ba6 !important;
            background-image: linear-gradient(135deg, #013ba6 0%, #228ddd 100%) !important;
        }

        .search-results-container {
            position: absolute;
            background-color: #fff;
            border: 1px solid #228ddd;
            box-shadow: 0 10px 25px rgba(1, 59, 166, 0.15);
            width: 30%;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .btn-primary {
            background-color: #013ba6 !important;
            border-color: #013ba6 !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #228ddd !important;
            border-color: #228ddd !important;
        }
        .text-primary {
            color: #013ba6 !important;
        }
        .text-brand-blue {
            color: #228ddd !important;
        }
        .text-brand-yellow {
            color: #f4ed2d !important;
        }
        .bg-brand-navy {
            background-color: #013ba6 !important;
        }
        .bg-brand-blue {
            background-color: #228ddd !important;
        }
        .bg-brand-yellow {
            background-color: #f4ed2d !important;
            color: #013ba6 !important;
        }
        .badge.bg-warning {
            background-color: #f4ed2d !important;
            color: #013ba6 !important;
            font-weight: 700;
        }
        .nav-fixed .topnav .navbar-brand {
            color: #013ba6 !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .topnav img {
            max-height: 42px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>

<body class="nav-fixed">
    @include('layouts.nav')

    <div id="layoutSidenav">
        {{-- @if (auth()->user()->hasRole('system-admin') && auth()->user()->user_type == 'staff')
        @include('layouts.sidebars.system-admin')
        @endif
        @if (auth()->user()->hasRole('admin') && auth()->user()->user_type == 'staff')
        @include('layouts.sidebars.admin')
        @endif
        @if (auth()->user()->hasRole('tutor') && auth()->user()->user_type == 'staff')
        @include('layouts.sidebars.tutor')
        @endif
        @if (auth()->user()->hasRole('student') && auth()->user()->user_type == 'student')
        @include('layouts.sidebars.student')
        @endif --}}
        @php
            $roles = ['system-admin', 'branch-admin', 'tutor', 'student'];
            $user = auth()->user();
        @endphp

        {{-- @foreach ($roles as $role)
            @if ($user->hasRole($role) && $user->user_type == ($role == 'student' ? 'student' : 'staff'))
                @include('layouts.sidebars.' . $role)
            @endif
        @endforeach --}}
        @include('layouts.sidebars.main-sidebar')


        <div id="layoutSidenav_content">
            <main>
                @yield('header')

                @include('layouts.components.alerts-html')

                <div class="container-xl px-4 {{ request()->routeIs('home') ? 'mt-n10' : '' }} ">
                    @yield('content')
                </div>
            </main>

            @include('layouts.footer')
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    @stack('chat_js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/datatables/datatables-simple-demo.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.3/sweetalert2.min.js"></script>

    @include('layouts.components.notify-js')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('imageViewer', (src = '') => {
                return {
                    imageUrl: src,

                    refreshUrl() {
                        this.imageUrl = this.$el.getAttribute("image-url")
                    },
                    fileChosen(event) {
                        this.fileToDataUrl(event, src => this.imageUrl = src)
                    },
                    fileToDataUrl(event, callback) {
                        if (!event.target.files.length) return

                        let file = event.target.files[0],
                            reader = new FileReader()
                        reader.readAsDataURL(file)
                        reader.onload = e => callback(e.target.result)
                    },
                }
            })
        })
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
            });
        });
    </script>
    @stack('js')


</body>

</html>
