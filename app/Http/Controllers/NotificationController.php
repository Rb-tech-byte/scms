<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Traits\SocialSmartTechConstants;

class NotificationController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {
        if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        $search = $request->get('search', '');

        $notifications = Notification::search($search)
            ->latest()
            ->paginate(999999999999)
            ->withQueryString();

        return view(
            'app.notifications.index',
            compact('notifications', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        return view('app.notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'applicant_id' => ['nullable', 'max:255'],
            'user_id' => ['nullable', 'max:255'],
            'message' => ['required', 'max:255', 'string'],
        ]);

        $notification = Notification::create($validated);

        return redirect()
            ->route('notifications.edit', $notification)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Notification $notification)
    {
        if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        return view('app.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Notification $notification)
    {
         if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        return view('app.notifications.edit', compact('notification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Notification $notification
    ): RedirectResponse {
         if (!$this->hasPermission('View notifications')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'applicant_id' => ['nullable', 'max:255'],
            'user_id' => ['nullable', 'max:255'],
            'message' => ['required', 'max:255', 'string'],
        ]);

        $notification->update($validated);

        return redirect()
            ->route('notifications.edit', $notification)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Notification $notification
    ) {
        if (!$this->hasPermission('Delete notifications')) {
            return redirect('permission-denied');
        }

        $notification->delete();
        echo 1;

        // return redirect()
        //     ->route('notifications.index')
        //     ->withSuccess(__('crud.common.removed'));
    }

    public function show_message($id){
        if (!is_numeric($id)) {
            $id = tss($id);
        }
        $message = Message::find($id);
        if($message->opened == 0){
            $message->opened = 1;
            $message->opened_at = now();
            $message->status = 1;
            $message->save();
        }
        $sender = $message->sender_id;
        if ($sender) {
            $user = User::find($sender);
        }else{
            $user = null;
        }

        return view('app.notifications.show_message', compact('message', 'user'));
    }

    public function show_all_messages(){
        $messages = Message::where('receiver_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        //make all messages as read
        foreach($messages as $message){
            if($message->opened == 0){
                $message->opened = 1;
                $message->opened_at = now();
                $message->status = 1;
                $message->save();
            }
        }
        return view('app.notifications.index', compact('messages'));
    }
}
