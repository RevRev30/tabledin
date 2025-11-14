<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => Carbon::now()])->save();
        }

        return redirect()->route('login')->with('status', 'Email verified. You can now sign in.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required','email']]);
        $user = User::where('email', $data['email'])->first();
        if ($user && is_null($user->email_verified_at)) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return back()->with('status', 'If your email exists and is unverified, a new link was sent.');
    }
}


