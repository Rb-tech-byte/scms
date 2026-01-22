<?php
namespace App\Traits;

Trait SocialSmartTechConstants {

    public function user()
    {
        return auth()->user();
    }

    public function hasPermission($permission)
    {

        if($this->user()->hasRole('system-admin')) return true;
        return $this->user()->hasPermission(strtolower(str_replace(' ','-',trim($permission))));

    }

}
