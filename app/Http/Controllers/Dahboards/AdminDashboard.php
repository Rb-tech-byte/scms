<?php

namespace App\Http\Controllers\Dahboards;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Applicant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(["auth","branch.access"]);
    }

    public function index(): View
    {
        if(Auth::user()->hasRole('system-admin')){
            $this->data['students'] = Applicant::all();
        $this->data['courses'] = Course::all();
        $this->data['invoices'] = Invoice::all();
        //collect all payment amounts
        $this->data['payments'] = $payments = Payment::all()->pluck('amount')->toArray();
        //sum up the payments
        $this->data['total_payments'] = array_sum($payments);

        //collect all invoices balances
        $this->data['balances']=$balances = Invoice::all()->pluck('balance')->toArray();
        //sum up the balances
        $this->data['total_balances'] = array_sum($balances);
        }else if(Auth::user()->hasRole('branch-admin')){
            $user = Auth::user();
            $branchId = session('user_branch_id') ?: $user->branch_id;
            $branch = $branchId ? Branch::find($branchId) : null;
            if (!$branch) {
                abort(403, 'You are not assigned to any branch.');
            }

            $student = Applicant::where('branch_id', $branch->id);
            // session(['branch'=>$branch]);
        $this->data['courses'] = Course::all();
        $studentIds = $student->pluck('id');
        $this->data['invoices'] = $studentIds->isNotEmpty() ? Invoice::whereIn('applicant_id', $studentIds)->get() : collect();
        //collect all payment amounts
        $this->data['payments'] = $payments = Payment::whereIn('applicant_id', $studentIds)->pluck('amount')->toArray();
        //sum up the payments
        $this->data['total_payments'] = array_sum($payments);

        //collect all invoices balances
        $this->data['balances']=$balances = Invoice::whereIn('applicant_id', $studentIds)->pluck('balance')->toArray();
        //sum up the balances
        $this->data['total_balances'] = array_sum($balances);
        $this->data['students'] = $student->get();
        $this->data['branch'] =$branch;

        }else{
            return redirect('permission-denied');
        }

        return view('dashboards.super_admin', $this->data);
    }


    }

