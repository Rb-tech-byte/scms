<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\RedirectResponse;
use App\Models\Permission;
use App\Traits\SocialSmartTechConstants;

class PermissionController extends Controller
{

    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('List permissions')) {
            return redirect('permission-denied');
        }

        $search = $request->get('search', '');
        $permissions = Permission::where('name', 'like', "%{$search}%")->get();

        return view('app.permissions.index')
            ->with('permissions', $permissions)
            ->with('search', $search);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        if (!$this->hasPermission('Add permissions')) {
            return redirect('permission-denied');
        }
        return redirect()->back()->with('warning', 'Contact Development Team to create new permissions');
        // $roles = Role::all();
        // return view('app.permissions.create')->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {

        if (!$this->hasPermission('Add permissions')) {
            return redirect('permission-denied');
        }
        return redirect()->back()->with('warning', 'Contact Development Team to create new permissions');

        // $data = $this->validate($request, [
        //     'name' => 'required|max:64',
        //     'roles' => 'array'
        // ]);
        // $data['name'] = trim($data['name']);

        // $permission = Permission::create($data);

        // $roles = Role::find($request->roles);
        // $permission->syncRoles($roles);

        // return redirect()
        //     ->route('permissions.edit', $permission->id)
        //     ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {

        if (!$this->hasPermission('View permissions')) {
            return redirect('permission-denied');
        }
        // return redirect()->back();

        return view('app.permissions.show')->with('permission', $permission);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission): RedirectResponse
    {
        if (!$this->hasPermission('Update permissions')) {
            return redirect('permission-denied');
        }
        return redirect()->back()->with('warning', 'Contact Development Team to Edit permissions');
        // $roles = Role::get();

        // return view('app.permissions.edit')
        //     ->with('permission', $permission)
        //     ->with('roles', $roles);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        if (!$this->hasPermission('Update permissions')) {
            return redirect('permission-denied');
        }
        return redirect()->back()->with('warning', 'Contact Development Team to Update permissions');
        // $data = $this->validate($request, [
        //     'name' => 'required|max:40',
        //     'roles' => 'array'
        // ]);
        // $data['name'] = trim($data['name']);

        // $permission->update($data);

        // $roles = Role::find($request->roles);
        // $permission->syncRoles($roles);

        // return redirect()
        //     ->route('permissions.edit', $permission->id)
        //     ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        if (!$this->hasPermission('Delete permissions')) {
            return redirect('permission-denied');
        }
        echo "System Permissions cannot be deleted";
        // return redirect()->back()->with('error', 'System Permissions cannot be deleted !');
        // $permission->delete();

        // return redirect()
        //     ->route('permissions.index')
        //     ->withSuccess(__('crud.common.removed'));
    }

    public function manage_permission($role_id = null)
    {
        if (!$this->hasPermission('List permissions')) {
            return redirect('permission-denied');
        }
        $rd_segment =request()->segment(3);
        $role_id = !empty($rd_segment)? decrypt($rd_segment):'';
        // dd($role_id);
        $role = !empty($rd_segment)?Role::find($role_id):'';
        $user_permissions = !empty($role) ? $role->permissions()->get() : [];
        $permissions = Permission::all();
        $roles = Role::all();

        return view('app.permissions.manage', compact(['permissions', 'roles', 'user_permissions', 'role_id']));
    }

    function assignPermissionRole(){
        $data = request()->except('method');
        $method = request('method');
        if($method == 'create'){
            $data =array_merge($data,['granted'=>'yes']);
            RolePermission::updateOrCreate($data);
            allocatePermission(request('role_id'),null,request('permission_id'));
            echo 'Permission Assigned';
        }else if($method =='destroy'){
            RolePermission::where($data)->delete();
           deallocatePermission(request('role_id'),null,request('permission_id'));
            echo 'Permission Removed';
        }else{
            echo "Unsupported Action";
        }
    }


    public function encryptRole(Request $request)
    {
        $role_id = $request->input('role_id');
        $encrypted = encrypt($role_id);

        echo $encrypted;
    }
}
