<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        // Find user and verify
        $user = User::findOrFail($id);

        // If already verified, redirect
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Mark verified and fire event
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('dashboard')->with('verified', true);
    }
}
