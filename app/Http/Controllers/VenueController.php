<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;

class VenueController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('List venues')) {
            return redirect('permission-denied');
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
        if (!$this->hasPermission('Add venues')) {
            return redirect('permission-denied');
        }

        $branches = Branch::pluck('name', 'id');

        return view('app.venues.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Add venues')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
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
        if (!$this->hasPermission('View venues')) {
            return redirect('permission-denied');
        }

        return view('app.venues.show', compact('venue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Venue $venue)
    {
        if (!$this->hasPermission('Update venues')) {
            return redirect('permission-denied');
        }

        $branches = Branch::pluck('name', 'id');

        return view('app.venues.edit', compact('venue', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        if (!$this->hasPermission('Update venues')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
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
        if (!$this->hasPermission('Delete venues')) {
            return redirect('permission-denied');
        }

        $venue->delete();
        echo 1;

        // return redirect()
        //     ->route('venues.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
