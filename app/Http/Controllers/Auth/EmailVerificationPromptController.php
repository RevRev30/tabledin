<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt.
     */
    public function __invoke(Request $request)
    {
        // If user is authenticated and already verified, send them back to dashboard
        if ($request->user() && method_exists($request->user(), 'hasVerifiedEmail') && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Return a dedicated view if it exists
        if (view()->exists('auth.verify-email')) {
            return view('auth.verify-email');
        }

        // Fallback plain response if no view is available
        return response('Please verify your email address. Check your inbox for the verification link.', 200);
    }
}
