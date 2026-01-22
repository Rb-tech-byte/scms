<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;

class CountryController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('List countries')) {
            return redirect('permission-denied');
        }

        $countries = Country::get();

        return view('app.countries.index', compact('countries', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         if (!$this->hasPermission('Add countries')) {
            return redirect('permission-denied');
        }

        return view('app.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
         if (!$this->hasPermission('Add countries')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'nicename' => ['nullable', 'max:255', 'string'],
            'iso' => ['nullable', 'max:255'],
            'iso3' => ['nullable', 'max:255', 'string'],
        ]);

        $country = Country::create($validated);

        return redirect()
            ->route('countries.edit', $country)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Country $country)
    {
        if (!$this->hasPermission('View countries')) {
            return redirect('permission-denied');
        }

        return view('app.countries.show', compact('country'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Country $country)
    {
         if (!$this->hasPermission('Update countries')) {
            return redirect('permission-denied');
        }

        return view('app.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country): RedirectResponse
    {
         if (!$this->hasPermission('Update countries')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'nicename' => ['nullable', 'max:255', 'string'],
            'iso' => ['nullable', 'max:255'],
            'iso3' => ['nullable', 'max:255', 'string'],
        ]);

        $country->update($validated);

        return redirect()
            ->route('countries.edit', $country)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Country $country
    ){
        if (!$this->hasPermission('Delete countries')) {
            return redirect('permission-denied');
        }

        $country->delete();
        echo 1;

        // return redirect()
        //     ->route('countries.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
}
