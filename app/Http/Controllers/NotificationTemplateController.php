<?php

namespace App\Http\Controllers;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        return view('notifications.templates.index');
    }
}
