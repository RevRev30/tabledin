<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StaffCustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        // Use a query builder so we can paginate and optionally filter
        $query = User::where('role', 'customer');

        // Optional simple search (by name or email)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Paginate so the view can call ->appends() and ->links()
        $customers = $query->orderBy('name')->paginate(15);

        // Keep query string on paginator (optional, view also does this)
        $customers->appends($request->query());

        return view('staff.customers', compact('customers'));
    }
}
