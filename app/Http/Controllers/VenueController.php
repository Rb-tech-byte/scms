<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;
use App\Services\PermissionService;

class VenueController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!PermissionService::can('List venues')) {
            abort(403, 'Unauthorized');
        }

        $search = $request->get('search', '');

        $venues = Venue::search($search)
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view('app.venues.index', compact('venues', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!PermissionService::canCreate('Add venues')) {
            abort(403, 'Unauthorized');
        }

        $branches = Branch::whereIn('id', PermissionService::getAccessibleBranchIds())->pluck('name', 'id');

        return view('app.venues.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!PermissionService::canCreate('Add venues')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
        
        // Force branch_id for non-system admins
        if (!PermissionService::isSystemAdmin()) {
            $validated['branch_id'] = session('user_branch_id');
        }
        
        $validated['name'] =  ucfirst(strtolower($validated['name']));

        Venue::create($validated);

        return redirect()
            ->route('venues.index')
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Venue $venue)
    {
        if (!PermissionService::canView('View venues', $venue)) {
            abort(403, 'Unauthorized');
        }

        return view('app.venues.show', compact('venue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Venue $venue)
    {
        if (!PermissionService::canUpdate('Update venues', $venue)) {
            abort(403, 'Unauthorized');
        }

        $branches = Branch::whereIn('id', PermissionService::getAccessibleBranchIds())->pluck('name', 'id');

        return view('app.venues.edit', compact('venue', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        if (!PermissionService::canUpdate('Update venues', $venue)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
        
        // Force branch_id for non-system admins
        if (!PermissionService::isSystemAdmin()) {
            $validated['branch_id'] = session('user_branch_id');
        }
        
        $venue->update($validated);

        return redirect()
            ->route('venues.index')
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Venue $venue)
    {
        if (!PermissionService::canDelete('Delete venues', $venue)) {
            abort(403, 'Unauthorized');
        }

        $venue->delete();
        echo 1;

        // return redirect()
        //     ->route('venues.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
