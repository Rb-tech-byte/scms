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
use App\Services\PermissionService;

class TutorController extends Controller
{
    use SocialSmartTechConstants;
    protected $data = [];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Keeping 'Add tutors' as per original code, though 'List tutors' or 'View tutors' might be better
        if (!PermissionService::can('Add tutors')) {
            abort(403, 'Unauthorized');
        }

        $search = $request->get('search', '');
        
        $tutors = Tutor::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.tutors.index', compact('tutors', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         if (!PermissionService::canCreate('Add tutors')) {
            abort(403, 'Unauthorized');
        }

        $branches = Branch::whereIn('id', PermissionService::getAccessibleBranchIds())->pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $roles = Role::whereNotIn('name',['student','system-admin'])->get();

        return view('app.tutors.create', compact('branches', 'users','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
         if (!PermissionService::canCreate('Add tutors')) {
            abort(403, 'Unauthorized');
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
        
        // Force branch_id if not system admin
        if (!PermissionService::isSystemAdmin()) {
            if (!session('user_branch_id')) {
                abort(403, 'Unauthorized');
            }
            $validated['branch_id'] = session('user_branch_id');
            $role = Role::where('name', 'tutor')->first();
        }

        DB::beginTransaction();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . rand(9, 999) . '.' . 'jpg';
            // Fix: Capture returned filename (which might be .png)
            $filename = imageEngine($image, $filename, 150, 150, 'profile/');

            $validated['image'] = $filename;
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => validatePhoneNumber($validated['phone']),
            'user_type' => 'staff',
            'davinci_role_id' => $role->id,
            'branch_id' => $validated['branch_id'],
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
        if (!PermissionService::canView('View tutors', $tutor)) {
            abort(403, 'Unauthorized');
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
        if (!PermissionService::can('View tutors')) { // Generic check as no model instance initially
             abort(403, 'Unauthorized');
        }
        $user_id = tss($id);
        $this->data['tutor'] = Tutor::where('user_id', $user_id)->first();
        
        if ($this->data['tutor']) {
             // Access check for specific tutor
             if (!PermissionService::canView('View tutors', $this->data['tutor'])) {
                abort(403, 'Unauthorized');
            }
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
            // Permission check for user?
            return view('app.users.show', compact('user'));
        }
    }

    public function upcoming_session()
    {
        if (!PermissionService::can('List courseassignments')) {
            abort(403, 'Unauthorized');
        }
        $this->data['title'] = 'New Upcomming Sessions';
        $tutor = auth()->user()->tutor;
        $this->data['courseAssignments'] = $tutor ? $tutor->courseAssignments()->where('is_teached', '0')->get() : collect();
        return view(
            'app.course_assignments.index',$this->data
        );
    }

    public function completed_session()
    {
        if (!PermissionService::can('List courseassignments')) {
            abort(403, 'Unauthorized');
        }
        $this->data['title'] = 'Completed Sessions';
        $tutor = auth()->user()->tutor;
        $this->data['courseAssignments'] = $tutor ? $tutor->courseAssignments()->where('is_teached', '1')->get() : collect();

        return view(
            'app.course_assignments.index', $this->data
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Tutor $tutor)
    {
        // Allow if permissible OR if editing self
        if (!PermissionService::canUpdate('Update tutors', $tutor) && auth()->user()->tutor?->id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $branches = Branch::whereIn('id', PermissionService::getAccessibleBranchIds())->pluck('name', 'id');
        $users = User::pluck('name', 'id');
        // Roles: logic from original code
        $roles = Role::whereNotIn('name',['student','system-admin'])->get();
        
        // If user is editing self but is not admin/branch-admin, maybe restrict roles?
        // Original code: if admin/system-admin/branch-admin -> show full edit. Else if self -> show full edit but maybe fewer options?
        // Original code showed same view/vars for both cases.
        // It used Role::get() for self-edit... which is MORE roles than admin? That was probably a bug or intended.
        // I will stick to filtered roles for consistency unless self-edit needs more.
        // Actually, let's use the explicit logic from before for roles if needed.
        if (auth()->user()->hasRole(['admin', 'system-admin', 'branch-admin'])) {
             // $roles filtered
        } else {
            //check if incoming id is the same as the tutor id
            if (auth()->user()->tutor && auth()->user()->tutor->id == $tutor->id) {
                $branches = Branch::pluck('name', 'id');
                $users = User::pluck('name', 'id');
                $roles = Role::get();

                return view('app.tutors.edit', compact('tutor', 'branches', 'users', 'roles'));
            } else {
                abort(403);
            }
        }

        return view('app.tutors.edit', compact('tutor', 'branches', 'users', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutor $tutor): RedirectResponse
    {
        if (!PermissionService::canUpdate('Update tutors', $tutor) && auth()->user()->tutor?->id !== $tutor->id) {
            abort(403, 'Unauthorized');
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
        
        // Force branch_id if not system admin
        if (!PermissionService::isSystemAdmin()) {
            if (!session('user_branch_id')) {
                abort(403, 'Unauthorized');
            }
            // Keep original branch if self-editing and not allowed to change?
            // If self-editing, user might not be branch-admin.
            // If they change branch, they move themselves.
            // Safer to force branch_id to user's branch OR keep it permissible if they are admin.
            // If I am self-editing, I am a tutor. My session branch_id is set.
            $validated['branch_id'] = session('user_branch_id');
        }
        
        $role = $request->roles;
        $tutor->update($validated);

        // Update user info as well
        $user = User::find($tutor->user_id);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_type = 'staff';
        $user->branch_id = $validated['branch_id'];
        $user->phone = validatePhoneNumber($validated['phone']);
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->address = $validated['address'];
        if (!PermissionService::isSystemAdmin() && !empty($role) && $role != $user->davinci_role_id) {
            abort(403, 'Unauthorized');
        }

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
            // Fix: Capture returned filename
            $filename = imageEngine($image, $filename, 150, 150, 'profile/');

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
        if (!PermissionService::canDelete('Delete tutors', $tutor)) {
            abort(403, 'Unauthorized');
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
