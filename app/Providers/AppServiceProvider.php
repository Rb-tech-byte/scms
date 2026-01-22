<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        Paginator::useBootstrap();
    }


    public function boot(): void
    {
        View::share('generatePageHeader', function ($pageTitle, $routeIndex, $icon1, $pageDescription = null, $icon2 = null, $routeAction = null, $icon3 = null, $action = null, $variable = null) {
            return view('components.page-header', compact('pageTitle', 'routeIndex', 'icon1', 'pageDescription', 'icon2', 'routeAction', 'icon3', 'action', 'variable'));
        });
        Blade::if('check', function ($permission) {
            if (auth()->user()->hasRole('system-admin'))
                return true;
            return auth()->check() && auth()->user()->hasPermission(strtolower(str_replace(' ', '-', trim($permission))));
        });

        Blade::if('checkBetween', function ($permission1,$permission2,$permission3=null) {
            if (auth()->user()->hasRole('system-admin')) return true;
            if(auth()->user()->hasPermission(strtolower(str_replace(' ', '-', trim($permission1))))) return true;
            if(auth()->user()->hasPermission(strtolower(str_replace(' ', '-', trim($permission2))))) return true;
            if($permission3 && auth()->user()->hasPermission(strtolower(str_replace(' ', '-', trim($permission3))))) return true;
            return false;

        });
    }
}
