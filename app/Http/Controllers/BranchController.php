<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;
use App\Services\PermissionService;

class BranchController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('List branches')) {
            return redirect('permission-denied');
        }
        $search = $request->get('search', '');

        $branchesQuery = Branch::search($search);

        if (!PermissionService::isSystemAdmin()) {
            $branchId = session('user_branch_id');
            if ($branchId) {
                $branchesQuery->where('id', $branchId);
            } else {
                $branchesQuery->whereRaw('1 = 0');
            }
        }

        $branches = $branchesQuery
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view('app.branches.index', compact('branches', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!$this->hasPermission('Register branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin()) {
            return redirect('permission-denied');
        }

        return view('app.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Register branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin()) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'location' => ['nullable', 'max:255', 'string'],
        ]);

        // $validated['name'] =  ucfirst(strtolower($validated['name']));
        $branch = Branch::create($validated);

        return redirect()
            ->route('branches.index')
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Branch $branch)
    {
        if (!$this->hasPermission('View branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin() && session('user_branch_id') != $branch->id) {
            abort(403, 'Unauthorized');
        }

        return view('app.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Branch $branch)
    {
         if (!$this->hasPermission('Update branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin() && session('user_branch_id') != $branch->id) {
            abort(403, 'Unauthorized');
        }

        return view('app.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch): RedirectResponse
    {
         if (!$this->hasPermission('Update branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin() && session('user_branch_id') != $branch->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'location' => ['nullable', 'max:255', 'string'],
        ]);

        // $validated['name'] =  ucfirst(strtolower($validated['name']));
        $branch->update($validated);

        return redirect()
            ->route('branches.edit', $branch)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Branch $branch)
    {
        if (!$this->hasPermission('Delete branches')) {
            return redirect('permission-denied');
        }

        if (!PermissionService::isSystemAdmin()) {
            return redirect('permission-denied');
        }

        $branch->delete();
        echo 1;

        // return redirect()
        //     ->route('branches.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
