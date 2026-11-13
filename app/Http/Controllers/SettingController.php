<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Facades\Image;
use \Spatie\Activitylog\Models\Activity;
use App\Traits\SocialSmartTechConstants;

class SettingController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('List settings')) {
            return redirect('permission-denied');
        }

        $settings = Setting::first();
        if(empty($settings)){
            $settingData = [
                'system_name '=>'OPEN UNIVERSITY ONLINE COURSE REGISTRATION'
            ];
            $settings = Setting::create($settingData);
        }

        $id = $settings->id;
        return to_route('settings.edit',$id);


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Add settings')) {
            return redirect('permission-denied');
        }

        $settings = Setting::first();
        if(empty($settings)){
            $settingData = [
                'system_name '=>'OPEN UNIVERSITY ONLINE COURSE REGISTRATION'
            ];
            $settings = Setting::create($settingData);
        }

        $id = $settings->id;
        return to_route('settings.edit',$id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Add settings')) {
            return redirect('permission-denied');
        }

        $settings = Setting::first();
        if(empty($settings)){
            $settingData = [
                'system_name '=>'OPEN UNIVERSITY ONLINE COURSE REGISTRATION'
            ];
            $settings = Setting::create($settingData);
        }

        $id = $settings->id;
        return to_route('settings.edit',$id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Setting $setting)
    {
        if (!$this->hasPermission('View settings')) {
            return redirect('permission-denied');
        }
        $settings = Setting::first();
        if(empty($settings)){
            $settingData = [
                'system_name '=>'OPEN UNIVERSITY ONLINE COURSE REGISTRATION'
            ];
            $settings = Setting::create($settingData);
        }

        $id = $settings->id;
        return to_route('settings.edit',$id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Setting $setting)
    {
        if (!$this->hasPermission('Update settings')) {
            return redirect('permission-denied');
        }
//        activity()->log('Look mum, I logged something');
//    $activity = Activity::all()->last();
//    return $activity;

        return view('app.settings.edit', compact('setting'));
    }

    public function show_system_logs(Request $request)
    {
        if (!$this->hasPermission('View activity logs')) {
            return redirect('permission-denied');
        }
        try {
            $logs = Activity::latest()->get();
        } catch (\Exception $e) {
            $logs = null;
        }

        return view('app.settings.system_logs', compact('logs'));
    }

    //clear system logs
    public function clear_system_logs(Schedule $schedule)
    {
        if (!$this->hasPermission('View activity logs')) {
            return redirect('permission-denied');
        }
        if(auth()->user()->hasRole('system-admin')){
            try {
                Activity::truncate();
            } catch (\Exception $e) {
                return to_route('system.logs')
                    ->with('error','Error clearing system logs');
            }
        }else{
            return to_route('system.logs')->with('error','You are not authorized to perform this action');
        }
        return redirect()
            ->route('system.logs')
            ->with('success','System logs cleared successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting): RedirectResponse
    {
        if (!$this->hasPermission('Update settings')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'system_name' => ['nullable', 'max:255', 'string'],
            'system_logo' => ['nullable', 'max:2048', 'image'],
            'allow_dark_sidebar' => ['nullable', 'boolean'],
            'allow_message' => ['nullable', 'boolean'],
            'allow_email' => ['nullable', 'boolean'],
            'enable_upload_receipt' => ['nullable', 'boolean'],
            'mail_mailer' => ['nullable', 'max:255', 'string'],
            'mail_host' => ['nullable', 'max:255', 'string'],
            'mail_port' => ['nullable', 'max:255', 'string'],
            'mail_username' => ['nullable', 'max:255', 'string'],
            'mail_password' => ['nullable', 'max:255', 'string'],
            'mail_encryption' => ['nullable', 'max:255', 'string'],
            'mail_from_address' => ['nullable', 'max:255', 'string'],
            'site_description' => ['nullable', 'max:255', 'string'],
            'site_contact_email' => ['nullable', 'max:255', 'string'],
            'site_contact_number' => ['nullable', 'max:255', 'string'],
        ]);

        if ($request->hasFile('system_logo')) {
            $image = $request->file('system_logo');
            $allowedExtensions = ['jpeg', 'png', 'jpg', 'webp'];
            $extension = strtolower($image->guessExtension() ?: $image->extension());
            if (!in_array($extension, $allowedExtensions, true)) {
                return back()->with('error', 'Invalid logo format. Allowed: JPG, PNG, WEBP.');
            }
            $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
            $filename = 'logo_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $safeExt;
            $driverClass = extension_loaded('imagick')
                ? \Intervention\Image\Drivers\Imagick\Driver::class
                : \Intervention\Image\Drivers\Gd\Driver::class;
            $manager = \Intervention\Image\ImageManager::usingDriver($driverClass);
            $image_resize = $manager->decodePath($image->getRealPath());
            $image_resize->scale(height: 32);
            $encoded = match ($safeExt) {
                'png' => $image_resize->encodeUsingFormat(\Intervention\Image\Format::PNG),
                'webp' => $image_resize->encodeUsingFormat(\Intervention\Image\Format::WEBP, quality: 90),
                default => $image_resize->encodeUsingFormat(\Intervention\Image\Format::JPEG, quality: 90),
            };
            $encoded->save(storage_path('app/public/' . $filename));
            if ($setting->system_logo) {
                if (file_exists(storage_path('app/public/' . $setting->system_logo))) {
                    @unlink(storage_path('app/public/' . $setting->system_logo));
                }
            }
            $validated['system_logo'] = $filename;
        }
        $setting->update(trimValues($validated));

        return redirect()
            ->route('settings.edit', $setting)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Setting $setting
    ){
        if (!$this->hasPermission('Delete settings')) {
            return redirect('permission-denied');
        }

        $setting->delete();
        echo 1;

        // return redirect()
            // ->route('settings.index')
            // ->withSuccess(__('crud.common.removed'));
    }
}
