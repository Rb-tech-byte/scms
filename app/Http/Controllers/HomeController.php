<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Traits\SocialSmartTechConstants;

class HomeController extends Controller
{
    use SocialSmartTechConstants;
    protected $data=[];

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {


        $user = auth()->user();
        $branch = $user->tutor?->branch;
        session(['branch'=>$branch]);
        if ($user->hasRole('system-admin') || $user->hasRole('branch-admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('tutor')) {
            return $this->tutorDashboard();
        } elseif ($user->hasRole('student')) {
            return $this->studentDashboard();
        } else {
            return $this->guestDashboard();
        }

    }



    public function adminDashboard()
    {
        if (!$this->hasPermission('view-dashboard')) {
            return redirect('permission-denied');
        }
        $dashboard = new \App\Http\Controllers\Dahboards\AdminDashboard();
        return $dashboard->index();

    }

    public function tutorDashboard()
    {
        $dashboard = new \App\Http\Controllers\Dahboards\TutorDashboard();
        return $dashboard->index();
    }

    public function studentDashboard()
    {
        $dashboard = new \App\Http\Controllers\Dahboards\StudentDashboard();
        return $dashboard->index();
    }

    public function guestDashboard(){
        return view('dashboards.no-dashboard', $this->data);
    }
}
