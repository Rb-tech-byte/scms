<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:; img-src 'self' data: https:; connect-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;">
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>@yield('page-title')</title>
         <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-white">
        <div id="layoutError">
            <div id="layoutError_content">
                <main>
                    <div class="container-xl px-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="text-center mt-4">
                                    @yield('image')
                                    <div class="lead">@yield('message')</div>
                                    <div class="mt-3">
                                        <button class="btn btn-primary text-arrow-icon" onclick="goBack()">
                                            <i class="ms-0 me-1" data-feather="arrow-left"></i>
                                            Go back
                                        </button>
                                        @yield('actions')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutError_footer">
                <footer class="footer-admin mt-auto footer-light">
                    <div class="container-xl px-4">
                        <div class="row">
                            <div class="col-md-6 small">Copyright &copy; SCMS {{date('Y')}} </div>
                            <div class="col-md-6 text-md-end small">
                                <a href="https://socialsmarttech.com">OUT SCMS</a>
                                &middot;
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/scripts.js')}}"></script>
        <script>
        function goBack() {
            window.history.back();
        }

    </script>
    </body>
</html>
