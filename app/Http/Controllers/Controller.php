<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\SocialSmartTechConstants;

/**
 * Class Controller
 *
 * Base controller for the Short Course Management System (SCMS).
 *
 * @package App\Http\Controllers
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, SocialSmartTechConstants;
}
