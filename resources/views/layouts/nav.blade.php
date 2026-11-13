<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white"
    id="sidenavAccordion">
    @php
        $setting = \App\Models\Setting::first();
    @endphp

    <a class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle">
        @php
        $logoSrc = systemLogoUrl($setting->system_logo ?? null);
        echo '<img src="' . $logoSrc . '" alt="Logo" style="height: 38px; width: 38px; object-fit: contain;" />';
        @endphp
       </a>

    <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="{{route('home')}}">

        {{$setting->system_name ?? 'OPEN UNIVERSITY'}}
    </a>

    @if(auth()->user()->hasRole(['branch-admin','system-admin']))
    <form class="form-inline me-auto d-none d-lg-block me-3">
        <div class="input-group input-group-joined input-group-solid">
            <input class="form-control pe-0" id="global_search" type="search" placeholder="Search.." aria-label="Search" />
            <div class="input-group-text"><i data-feather="search"></i></div>
        </div>
        <div id="search-results" class="search-results-container dropdown-menu dropdown-menu-center border-0 shadow animated--fade-in-up"></div>
    </form>
    
    @endif

    <ul class="navbar-nav align-items-center ms-auto">

        @include('layouts.components.search')
         @include('layouts.components.quick-actions')


        @include('layouts.components.alerts')
        @include('layouts.components.messages')
        <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage"
                href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><img class="img-fluid"
                    src="{{ profileImageUrl(Auth::user()->image, asset('assets/assets/img/favicon.png')) }}" /></a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">

                    <img class="dropdown-user-img" src="{{ profileImageUrl(Auth::user()->image, asset('assets/assets/img/favicon.png')) }}" />

                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">{{Auth::user()->name}}</div>
                        <div class="dropdown-user-details-email"><span class="badge bg-primary">{{Auth::user()->role()->name ?? 'user'}}</span></div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                @auth
                <a class="dropdown-item" href="{{route('users.show',Auth::user()->id)}}">
                    <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                    Account
                </a>
                @endauth
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
            </div>
        </li>
    </ul>
</nav>
