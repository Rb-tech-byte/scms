<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Traits\SocialSmartTechConstants;
use Illuminate\Validation\Rules\Password;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    use SocialSmartTechConstants;
    private $data = [];

    public function index(Request $request)
    {
        if (!$this->hasPermission('Manage staffs')) {
            return redirect('permission-denied');
        }
        $users = User::where('user_type', 'staff')->latest()->get();
        return view('app.users.index', compact('users'));
    }


    public function create(Request $request)
    {
        if (!$this->hasPermission('Add tutors')) {
            return redirect('permission-denied');
        }
        $roles = Role::where('name','system-admin')->get();

        return view('app.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Add tutors')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:users,email', 'email'],
            'phone' => ['nullable', 'max:255', 'string'],
            'address' => ['nullable', 'max:255', 'string'],
            'password' => ['required',Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'image' => ['nullable', 'image', 'max:1024'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['user_type'] = 'staff';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . rand(111, 999) . '.' . 'jpg';
            imageEngine($image, $name, 150, 150, 'profile/');
            $validated['image'] = $name;
        }
        $validated['phone'] = validatePhoneNumber($validated['phone']);


        $user = User::create($validated);

        $user->assignRole($request->davinci_role_id);
        allocatePermission($request->davinci_role_id,$user->id);

        return redirect()
            ->route('users.edit', $user)
            ->withSuccess(__('crud.common.created'));
    }


    public function show(Request $request, User $user)
    {
        if (!$this->hasPermission('View tutors')) {
            return redirect('permission-denied');
        }
        if (Auth::user()->hasRole('tutor')) {
            $tutor = \App\Models\Tutor::where('user_id', Auth::user()->id)->first();
            return redirect()->route('tutors.show', $tutor->id);
        }
        try {
            $logs = Activity::where('causer_id', $user->id)
                ->latest()
                ->get();
        } catch (\Exception $e) {
            $logs = null;
        }
        return view('app.users.show', compact('user','logs'));
    }

    public function edit(Request $request, User $user)
    {
        if (!$this->hasPermission('Update tutors')) {
            return redirect('permission-denied');
        }

        $this->data['roles'] = Role::get();
        $this->data['user'] = $user;
        $this->data['permissions'] = Permission::all();

        return view('app.users.edit', $this->data);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (!$this->hasPermission('Update tutors')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($user),
                'email',
            ],
            'phone' => ['nullable', 'max:255', 'string'],
            'address' => ['nullable', 'max:255', 'string'],
            'image' => ['nullable', 'image', 'max:1024'],
            'davinci_role_id' => ['required', 'exists:davinci_roles,id']
        ]);


        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $request->validate(['password'=>[Password::min(8)->uncompromised()->mixedCase()->letters()->numbers()->symbols()]]);
            $validated['password'] = Hash::make($validated['password']);
        }
        // dd($validated);

        if ($request->hasFile('image')) {
            deleteImage($user->image, 'profile/');
            $image = $request->file('image');
            $name = time() . rand(111, 999) . '.' . 'jpg';
            imageEngine($image, $name, 150, 150, 'profile/');
            $validated['image'] = $name;
        }
        // $validated['name'] =  ucfirst(strtolower($validated['name']));
        $validated['user_type'] = 'staff';
        $user->update($validated);

        $check_if_user_is_tutor = \App\Models\Tutor::where('user_id', $user->id)->first();
        if ($check_if_user_is_tutor) {
            $check_if_user_is_tutor->update($validated);
        }
        if (!empty($validated['password'])) {
            auth()->logout();
        }
        return redirect()
            ->route('users.show', $user)
            ->withSuccess(__('crud.common.saved'));
    }

    public function updatePermissions(Request $request, string $id)
    {
        if ($this->hasPermission('Update permissions')) {


            $user = User::findOrFail($id);
            $user->permissions()->detach();
            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    $user->permissions()->attach($permission, ['granted' => 'yes']);
                }
            }
            return back()->with('success', 'Permissions updated successfully');
        } else {
            return redirect('permission-denied');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (!$this->hasPermission('Delete tutors')) {
            return redirect('permission-denied');
        }

        if ($user->image) {
            Storage::delete($user->image);
        }
        deallocatePermission($user->davinci_role_id,$user->id);
        $user->delete();
        echo 1;

        // return redirect()
        //     ->route('users.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
