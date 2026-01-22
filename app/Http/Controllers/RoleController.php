<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;


class RoleController extends Controller {

    use SocialSmartTechConstants;

    public function __construct(){
        $this->middleware("access:list-roles")->only('index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $roles = Role::where('name', 'like', "%{$search}%")->get();

        return view('app.roles.index')
            ->with('roles', $roles)
            ->with('search', $search);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->hasPermission('add-roles')) {
            return redirect('permission-denied');
        }
        return redirect()->back()->with('warning', 'Contact Development Team to create new roles');
    }
    public function store(Request $request): RedirectResponse
    {

        if (!$this->hasPermission('add-roles')) {
            return redirect('permission-denied');
        }

        $data = $this->validate($request, [
            'name' => 'required|unique:roles|max:32',
            'permissions' => 'array',
        ]);
        $data['name'] = trim($data['name']);
        $role = Role::create($data);

        $permissions = Permission::find($request->permissions);
        $role->syncPermissions($permissions);

        return redirect()
            ->route('roles.edit', $role->id)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Role $role): View
    {
        if (!$this->hasPermission('view-roles')) {
            return redirect('permission-denied');
        }

        return view('app.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Role $role): View
    {
        if (!$this->hasPermission('update-roles')) {
            return redirect('permission-denied');
        }

        $permissions = Permission::all();

        return view('app.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if (!$this->hasPermission('update-roles')) {
            return redirect('permission-denied');
        }

        $data = $this->validate($request, [
            'name' => 'required|max:32|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $data['name'] = trim($data['name']);
        $role->update($data);

        if ($request->has('permissions')) {
            $permissions = Permission::find($request->permissions);
            $role->syncPermissions($permissions);
        }

        return redirect()
            ->route('roles.index')
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if (!$this->hasPermission('delete-roles')) {
            return redirect('permission-denied');
        }

        try {
            $role->delete();

            return redirect()
                ->route('roles.index')
                ->withSuccess(__('crud.common.removed'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Cannot delete this role. It may be assigned to users.']);
        }
    }
}
