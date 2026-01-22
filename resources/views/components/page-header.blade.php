<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-fluid px-4 pt-3">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="{{$icon1}}"></i></div>
                        {{ ucfirst($pageTitle) }} {{ $variable ?? '' }}
                    </h1>
                </div>
                <div class="col-12 col-xl-auto mb-3">
                    <a class="btn btn-sm btn-light text-primary" href="{{ route($routeIndex) }}">
                        <i class="me-1" data-feather="{{$icon2}}"></i>
                        {{ ucfirst($pageDescription) ?? 'Manage .' . ucfirst($pageTitle) . ''}}
                    </a>
                    @if(!empty($routeAction) && !empty($action) && $routeAction != '')
                    <a class="btn btn-sm btn-primary" href="{{ route($routeAction) }}">
                        <i class="me-1" data-feather="{{$icon3}}"></i>
                         {{ ucfirst($action) }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
