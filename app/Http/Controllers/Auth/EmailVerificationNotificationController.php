<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return back()->with('warning', 'Unable to send verification email.');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
