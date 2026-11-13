<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
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
use App\Services\PermissionService;

class UserController extends Controller
{
    use SocialSmartTechConstants;
    private $data = [];

    public function index(Request $request)
    {
        if (!PermissionService::can('Manage staffs')) {
            abort(403, 'Unauthorized');
        }

        $usersQuery = User::with(['branch']);

        $userType = $request->get('user_type', 'all');
        if (in_array($userType, ['staff', 'student'], true)) {
            $usersQuery->where('user_type', $userType);
        }

        if ($search = trim((string) $request->get('search', ''))) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->get('role_id')) {
            $usersQuery->where('davinci_role_id', $roleId);
        }

        if ($branchIdFilter = $request->get('branch_id')) {
            $usersQuery->where('branch_id', $branchIdFilter);
        }

        $status = $request->get('status');
        if ($status === 'blocked') {
            $usersQuery->where('is_blocked', true);
        } elseif ($status === 'active') {
            $usersQuery->where('is_blocked', false);
        }

        if (Auth::user()->hasRole(['branch-admin', 'training-coordinator'])) {
            $usersQuery->where('user_type', 'staff');
            $branchId = session('user_branch_id');
            if ($branchId) {
                $usersQuery->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->orWhereHas('tutor', function ($tq) use ($branchId) {
                            $tq->where('branch_id', $branchId);
                        });
                });
            } else {
                $usersQuery->whereRaw('1 = 0');
            }
        }

        $users = $usersQuery->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('app.users.index', compact('users', 'roles', 'branches'));
    }


    public function create(Request $request)
    {
        if (!PermissionService::can('Add tutors')) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('branch-admin')) {
            $roles = Role::whereIn('name', ['tutor'])->get();
        } else {
            $roles = Role::whereNotIn('name', ['student'])->get();
        }

        $branches = Branch::pluck('name', 'id');

        return view('app.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!PermissionService::can('Add tutors')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:users,email', 'email'],
            'phone' => ['nullable', 'max:255', 'string'],
            'address' => ['nullable', 'max:255', 'string'],
            'password' => ['required',Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'image' => ['nullable', 'image', 'max:1024'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'davinci_role_id' => ['required', 'exists:davinci_roles,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['user_type'] = 'staff';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . rand(111, 999) . '.' . 'jpg';
            // Fix: Capture returned filename
            $name = imageEngine($image, $name, 150, 150, 'profile/');
            $validated['image'] = $name;
        }
        $validated['phone'] = validatePhoneNumber($validated['phone']);

        if (!PermissionService::isSystemAdmin()) {
            $validated['branch_id'] = session('user_branch_id');
        }


        $user = User::create($validated);

        $role = Role::find($validated['davinci_role_id']);

        if ($role && $role->name !== 'system-admin' && empty($user->branch_id)) {
            $user->delete();
            return back()->withErrors(['branch_id' => 'Branch is required for staff users.'])->withInput();
        }

        if (Auth::user()->hasRole('branch-admin') && $role && in_array($role->name, ['system-admin', 'branch-admin'], true)) {
            $user->delete();
            abort(403, 'Unauthorized');
        }

        $user->assignRole($validated['davinci_role_id']);
        allocatePermission($validated['davinci_role_id'], $user->id);

        if ($role && $role->name === 'branch-admin' && empty($user->branch_id)) {
            $user->delete();
            return back()->withErrors(['branch_id' => 'Branch is required for branch-admin.'])->withInput();
        }

        return redirect()
            ->route('users.edit', $user)
            ->withSuccess(__('crud.common.created'));
    }


    public function show(Request $request, User $user)
    {
        if (!PermissionService::can('View tutors')) { // Generic check as Permissions on Users are complex
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('branch-admin')) {
            if ($user->hasRole(['system-admin', 'branch-admin'])) {
                abort(403, 'Unauthorized');
            }
            $branchId = session('user_branch_id');
            $userBranchId = $user->branch?->id;
            if (!$branchId || !$userBranchId || (int) $branchId !== (int) $userBranchId) {
                abort(403, 'Unauthorized');
            }
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
        if (!PermissionService::can('Update tutors')) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('branch-admin')) {
            if ($user->hasRole(['system-admin', 'branch-admin'])) {
                abort(403, 'Unauthorized');
            }
            $branchId = session('user_branch_id');
            $userBranchId = $user->branch?->id;
            if (!$branchId || !$userBranchId || (int) $branchId !== (int) $userBranchId) {
                abort(403, 'Unauthorized');
            }
        }

        if (Auth::user()->hasRole('branch-admin')) {
            $this->data['roles'] = Role::whereIn('name', ['tutor'])->get();
        } else {
            $this->data['roles'] = Role::whereNotIn('name', ['student'])->get();
        }
        $this->data['user'] = $user;
        $this->data['permissions'] = Permission::all();
        $this->data['branches'] = Branch::pluck('name', 'id');

        return view('app.users.edit', $this->data);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (!PermissionService::can('Update tutors')) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('branch-admin')) {
            if ($user->hasRole(['system-admin', 'branch-admin'])) {
                abort(403, 'Unauthorized');
            }
            $branchId = session('user_branch_id');
            $userBranchId = $user->branch?->id;
            if (!$branchId || !$userBranchId || (int) $branchId !== (int) $userBranchId) {
                abort(403, 'Unauthorized');
            }
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
            'davinci_role_id' => ['required', 'exists:davinci_roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
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
            // Fix: Capture returned filename
            $name = imageEngine($image, $name, 150, 150, 'profile/');
            $validated['image'] = $name;
        }
        // $validated['name'] =  ucfirst(strtolower($validated['name']));
        $validated['user_type'] = 'staff';

        if (!PermissionService::isSystemAdmin()) {
            $validated['branch_id'] = session('user_branch_id');
        }

        $role = Role::find($validated['davinci_role_id']);
        if (Auth::user()->hasRole('branch-admin') && $role && $role->name !== 'tutor') {
            abort(403, 'Unauthorized');
        }

        if ($role && $role->name !== 'system-admin' && empty($validated['branch_id'])) {
            return back()->withErrors(['branch_id' => 'Branch is required for staff users.'])->withInput();
        }
        if ($role && $role->name === 'branch-admin' && empty($validated['branch_id'])) {
            return back()->withErrors(['branch_id' => 'Branch is required for branch-admin.'])->withInput();
        }

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
        if (!PermissionService::can('Update permissions')) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);
        $user->permissions()->detach();
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permission) {
                $user->permissions()->attach($permission, ['granted' => 'yes']);
            }
        }
        return back()->with('success', 'Permissions updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (!PermissionService::can('Delete tutors')) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('branch-admin')) {
            if ($user->hasRole(['system-admin', 'branch-admin'])) {
                abort(403, 'Unauthorized');
            }
            $branchId = session('user_branch_id');
            $userBranchId = $user->branch?->id;
            if (!$branchId || !$userBranchId || (int) $branchId !== (int) $userBranchId) {
                abort(403, 'Unauthorized');
            }
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

    /**
     * Block or unblock a system user (SRS R3) and clear temporary security lockouts on unblock.
     */
    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        if (!PermissionService::can('block-users') && !PermissionService::can('Manage staffs')) {
            abort(403, 'Unauthorized');
        }

        if ((int) Auth::id() === (int) $user->id) {
            return back()->with('error', 'You cannot block your own active administrator account.');
        }

        $newState = !$user->is_blocked;
        $user->forceFill([
            'is_blocked' => $newState,
            'blocked_at' => $newState ? now() : null,
            'blocked_reason' => $newState ? ($request->input('reason') ?: 'Blocked by Systems Administrator') : null,
            'failed_login_attempts' => 0,
            'lockout_count' => 0,
            'locked_until' => null,
            'active_session_id' => $newState ? null : $user->active_session_id,
        ])->save();

        activity('security')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log($newState ? "Blocked user {$user->email}" : "Unblocked user {$user->email}");

        return back()->with('success', $newState ? "User {$user->name} has been blocked." : "User {$user->name} has been unblocked.");
    }
}
