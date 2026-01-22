<?php

namespace App\Http\Controllers;

class AnalyticsController extends Controller
{
    public function branches()
    {
        return view('analytics.branches');
    }

    public function discounts()
    {
        return view('analytics.discounts');
    }

    public function tutorWorkload()
    {
        return view('analytics.tutor-workload');
    }
}
