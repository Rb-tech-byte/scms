<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manages configurable email/SMS templates for overdue reminders and alerts.
 */
class NotificationTemplateController extends Controller
{
    public function index(): View
    {
        if (!PermissionService::can('view-notifications') && !PermissionService::can('manage-notification-templates')) {
            abort(403, 'Unauthorized');
        }

        $templates = NotificationTemplate::orderBy('name')->get();
        return view('notifications.templates.index', compact('templates'));
    }

    public function update(Request $request, NotificationTemplate $template): RedirectResponse
    {
        if (!PermissionService::can('view-notifications') && !PermissionService::can('manage-notification-templates')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'channel' => ['required', 'in:email,sms,both'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update([
            'channel' => $validated['channel'],
            'subject' => $validated['subject'] ?? $template->subject,
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Template '{$template->name}' updated.");
    }
}
