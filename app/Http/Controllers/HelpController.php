<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Provides the built-in Online Help Center and role guide (SRS 5.2.2 Usability #33).
 */
class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(): View
    {
        $user = Auth::user();
        $roleName = $user->role()?->name ?? 'user';

        return view('help.index', compact('user', 'roleName'));
    }
}

