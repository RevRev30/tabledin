<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class StaffRestaurantController extends Controller
{
    /**
     * Display a listing of restaurants for staff.
     */
    public function index(Request $request)
    {
        $restaurants = Restaurant::all();

        // Return the existing top-level view (resources/views/staff/restaurants.blade.php)
        if (view()->exists('staff.restaurants')) {
            return view('staff.restaurants', compact('restaurants'));
        }

        // Fallback if view not present
        return response()->json($restaurants);
    }
}
