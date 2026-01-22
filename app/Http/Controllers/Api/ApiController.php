<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Venue;
use Illuminate\Http\Request;
use App\Models\Country;

class ApiController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        $countries = Country::where('name', 'like', '%' . strtoupper($searchTerm) . '%')
            ->select('id', 'name','nicename','iso','iso3')
            ->get();
        return response()->json($countries);
    }

    public function fetchCourseFees($selectedCourseId)
    {
        $course = Course::where('id', $selectedCourseId)->first();

        if ($course) {
            return response()->json(['fee' => $course->fee,'duration'=>$course->duration,'has_discount'=>$course->has_discount,'discount'=>$course->discount]);
        } else {
            return response()->json(['fee' => null]);
        }
    }

    public function fetch_branch_by_tutor_id($tutor_id)
    {
        $tutor = \App\Models\Tutor::find($tutor_id);
        if(!$tutor){
            return response()->json(['error' => 'Tutor not found']);
        }
        $branch = \App\Models\Branch::find($tutor->branch_id);
        $venues = \App\Models\Venue::where('branch_id',$tutor->branch_id)->get();

        return response()->json(['branch' => $branch,'venues'=>$venues]);
    }

    public function fetch_venue_by_branch_id($branch_id)
    {
        $venues = \App\Models\Venue::where('branch_id',$branch_id)->get();
        return response()->json($venues);
    }


  public function fetchCourse(Request $request){
    $courses = Course::select(['title','id'])->get()->toArray();
    return response()->json($courses);
  }

  public function fetchBranch(Request $request){
    $branch = session('branch');
    $branches = empty($branch) ? Branch::select(['name','id'])->get()->toArray() :  Branch::select(['name','id'])->where('id',$branch->id)->get()->toArray();
    return response()->json($branches);
  }

  public function fetchVenue(Request $request){

    $venues = Venue::query();
    if($request->input('branch_id')){
        $venues = $venues->where('branch_id',$request->input('branch_id'));
    }
    return response()->json($venues->select(['name','id','branch_id'])->get()->toArray());
}

}
