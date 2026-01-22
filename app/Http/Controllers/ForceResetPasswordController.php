<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForceResetPasswordController extends Controller
{
    
    public function showForm()
    {
        $data =['error_msg'=>'Your Password does not comply with our Security Policy, please update it'];
        return view('auth.force_password_reset',$data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->uncompromised()->mixedCase()->numbers()->letters()->symbols()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->force_password_reset = false;
        $user->save();

        auth()->logout();

        return redirect()->route('login')->with('success', 'Password updated. Please log in.');
    }
}
