<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role ?? '', ['staff', 'admin'])) {
            return redirect()->route('landing')->with('error', 'You do not have access to that area.');
        }

        return $next($request);
    }
}
}
