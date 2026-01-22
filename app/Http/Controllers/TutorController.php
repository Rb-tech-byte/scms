<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tutor;
use App\Models\Branch;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Role;
use Intervention\Image\Facades\Image;
use \Spatie\Activitylog\Models\Activity;
use App\Traits\SocialSmartTechConstants;
use Illuminate\Support\Facades\DB;

class TutorController extends Controller
{
    use SocialSmartTechConstants;
    protected $data = [];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->hasPermission('Add tutors')) {
            return redirect('permission-denied');
        }

        $search = $request->get('search', '');
        $branch = session('branch');

        //check if user is admin or system-admin and return all tutors if true

        if (auth()->user()->hasRole(['admin', 'system-admin'])) {
            $tutors = Tutor::latest()->get();
        } else if(!empty($branch)) {
            $tutors = Tutor::where('branch_id', $branch->id)
                ->search($search)
                ->latest()
                ->paginate(5)
                ->withQueryString();
        }else{
            return redirect('permission-denied');
        }

        return view('app.tutors.index', compact('tutors', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

         if (!$this->hasPermission('Add tutors')) {
            return redirect('permission-denied');
        }

        $branches = Branch::pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $roles = Role::whereNotIn('name',['student','system-admin'])->get();

        return view('app.tutors.create', compact('branches', 'users','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
         if (!$this->hasPermission('Add tutors')) {
            return redirect('permission-denied');
        }
        $role_id = $request->roles;
        $role = empty($role_id) ? Role::where('name','tutor')->first():Role::find($role_id);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:users,email', 'email'],
            'phone' => ['nullable', 'unique:users,phone', 'max:255', 'string'],
            'address' => ['nullable', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
        ]);
        DB::beginTransaction();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . rand(9, 999) . '.' . 'jpg';
            imageEngine($image, $filename, 150, 150, 'profile/');

            $validated['image'] = $filename;
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => validatePhoneNumber($validated['phone']),
            'user_type' => 'staff',
            'davinci_role_id' => $role->id,
            'address' => $validated['address'],
            'password' => Hash::make('password'),
        ]);

        // $user->user->syncRoles(Role::findByName('tutor'));
        allocatePermission($user->davinci_role_id,$user->id);
        if(!empty($validated['image'])){
            $user->image=$validated['image'];
            $user->save();
            $user->refresh();
        }
        $tutor = Tutor::create( array_merge($validated, ['user_id' => $user->id]));
        $message = 'Dear ' . $tutor->name . ', Welcome to ' . config('app.name') . '. Your account has been created. Your login credentials are as follows: Email: ' . $tutor->email . ' Password: password';
        $data = [
            'sender_id' => auth()->user()->id,
            'receiver_id' => $tutor->user_id,
            'body' => $message,
            'opened' => 0,
            'status' => 0,
        ];
        $message = \App\Models\Message::create($data);

        DB::commit();

        return redirect()
            ->route('tutors.show', $tutor)
            ->with('success', $tutor->name . ' ' .'Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tutor $tutor)
    {
        if (!$this->hasPermission('View tutors')) {
            return redirect('permission-denied');
        }
        try {
            $logs = Activity::where('causer_id', $tutor->user_id)
                ->latest()
                ->get();
        } catch (\Exception $e) {
            $logs = null;
        }
        $this->data['tutor'] = $tutor;
        $this->data['courseAssignments'] = $tutor->courseAssignments()->get();
        $this->data['logs'] = $logs;

        return view('app.tutors.show', $this->data);
    }

    public function view_tutor($id)
    {
        if (!$this->hasPermission('View tutors')) {
            return redirect('permission-denied');
        }
        $user_id = tss($id);
        $this->data['tutor'] = Tutor::where('user_id', $user_id)->first();
        if ($this->data['tutor']) {
            try {
                $logs = Activity::where('causer_id', $user_id)
                    ->latest()
                    ->get();
            } catch (\Exception $e) {
                $logs = null;
            }
            $this->data['logs'] = $logs;
            $this->data['courseAssignments'] = $this->data['tutor']->courseAssignments()->get();
            return view('app.tutors.show', $this->data);
        } else {
            $user = User::find($user_id);
            return view('app.users.show', compact('user'));
        }
    }

    public function upcoming_session()
    {
        if (!$this->hasPermission('List courseassignments')) {
            return redirect('permission-denied');
        }
        $this->data['title'] = 'New Upcomming Sessions';
        $this->data['courseAssignments'] = auth()->user()->tutor->courseAssignments()->where('is_teached', '0')->get();
        return view(
            'app.course_assignments.index',$this->data
        );
    }

    public function completed_session()
    {
        if (!$this->hasPermission('List courseassignments')) {
            return redirect('permission-denied');
        }
        $this->data['title'] = 'Completed Sessions';
        $this->data['courseAssignments'] = auth()->user()->tutor->courseAssignments()->where('is_teached', '1')->get();

        return view(
            'app.course_assignments.index', $this->data
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Tutor $tutor)
    {
        if (!$this->hasPermission('Update tutors')) {
            return redirect('permission-denied');
        }
        $branch = session('branch');
        //check if user is admin or system-admin or branch_manager
        if (auth()->user()->hasRole(['admin', 'system-admin', 'branch-admin'])) {
            $branches = empty($branch) ? Branch::pluck('name', 'id'): Branch::where('id',$branch->id)->pluck('name','id');
            $users = User::pluck('name', 'id');
            $roles = Role::whereNotIn('name',['student','system-admin'])->get();

            return view('app.tutors.edit', compact('tutor', 'branches', 'users', 'roles'));
        } else {
            //check if incoming id is the same as the tutor id
            if (auth()->user()->tutor->id == $tutor->id) {
                $branches = Branch::pluck('name', 'id');
                $users = User::pluck('name', 'id');
                $roles = Role::get();

                return view('app.tutors.edit', compact('tutor', 'branches', 'users', 'roles'));
            } else {
                abort(403);
            }
        }


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutor $tutor): RedirectResponse
    {
        if (!$this->hasPermission('Update tutors')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($tutor->user_id),
                'email',
            ],
            'phone' => [
                'required',
                Rule::unique('users', 'phone')->ignore($tutor->user_id),
                'max:255',
                'string',
            ],
            'address' => ['nullable', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
            'password' => ['nullable', 'min:4', 'confirmed'],
            'active' => ['required', 'boolean'],
        ]);
        $role = $request->roles;
        $tutor->update($validated);

        // Update user info as well
        $user = User::find($tutor->user_id);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_type = 'staff';
        $user->phone = validatePhoneNumber($validated['phone']);
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->address = $validated['address'];
        if(isset($role) && $role != $user->davinci_role_id){
            deallocatePermission($user->davinci_role_id,$user->id);
            $user->davinci_role_id = $role;
            allocatePermission($role,$user->id);
        }
        
        $user->save();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . rand(9, 999) . '.' . 'jpg';
            deleteImage($user->image, 'profile/');
            imageEngine($image, $filename, 150, 150, 'profile/');

            $user->image = $filename;
            $user->save();
        }
        return to_route('tutors.show', $tutor)->with('success', $tutor->name . ' ' .'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tutor $tutor)
    {
        if (!$this->hasPermission('Delete tutors')) {
            return redirect('permission-denied');
        }

        $user = User::find($tutor->user_id);
        if ($user->image) {
            deleteImage($user->image, 'profile/');
        }
        deallocatePermission($user->davinci_role_id,$user->id);
        $user->delete();
        $tutor->delete();
        echo 1;

        // return redirect()
        //     ->route('tutors.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
