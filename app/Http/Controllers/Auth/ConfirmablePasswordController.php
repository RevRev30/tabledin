<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ConfirmablePasswordController extends Controller
{
    public function show(Request $request)
    {
        if (view()->exists('auth.confirm-password')) {
            return view('auth.confirm-password');
        }

        return response('Please confirm your password.', 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password does not match our records.']);
        }

        // Mark password as recently confirmed (Laravel uses this session key)
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    }
}
