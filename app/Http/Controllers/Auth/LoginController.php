<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (view()->exists('auth.login')) {
            return view('auth.login');
        }
        return response('Login page', 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Keep staff/admin on staff dashboard
            $user = Auth::user();
            if ($user && in_array($user->role ?? '', ['staff', 'admin'])) {
                return redirect()->route('staff.dashboard');
            }

            // Redirect customers to the main dashboard (change here if you prefer another page)
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}
