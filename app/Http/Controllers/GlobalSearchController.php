<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Branch;
use App\Models\Course;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Payment;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $branch = session('branch');
        if (empty($branch)) {
            $applicants = Applicant::search($searchTerm)->get();
            $tutors = Tutor::search($searchTerm)->get();
            $payments = Payment::search($searchTerm)->get();
            $courses = Course::search($searchTerm)->get();
            $venues = Venue::search($searchTerm)->get();
            $branches = Branch::search($searchTerm)->get();
        } else {

            $applicants = Applicant::search($searchTerm)->where('branch_id', $branch->id)->get();
            $tutors = Tutor::search($searchTerm)->where('branch_id', $branch->id)->get();
            $payments = Payment::search($searchTerm)->whereIn('applicant_id', Applicant::where('branch_id', $branch->id)->pluck('id'))->get();
            $courses = Course::search($searchTerm)->get();
            $venues = Venue::search($searchTerm)->where('branch_id', $branch->id)->get();
            $branches = Branch::search($searchTerm)->find($branch->id);
        }


        // Merge the results
        $results = (new Collection())
            ->merge($applicants)
            ->merge($tutors)
            ->merge($courses)
            ->merge($venues)
            ->merge($branches)
            ->merge($payments);

        return view('layouts.components.results', compact('results'));
    }
}
