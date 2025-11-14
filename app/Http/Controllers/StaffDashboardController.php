<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class StaffDashboardController extends Controller
{
    /**
     * Display staff dashboard summary.
     */
    public function index(Request $request)
    {
        // Collections so views can call ->count()
        $reservations = Reservation::all();

        // pending reservations as a collection
        $pendingReservations = Reservation::where('status', 'pending')->get();

        // totals
        $totalCustomers = User::where('role', 'customer')->count();
        $totalRestaurants = Restaurant::count();

        // Today's reservations collection (use reservation_date if available)
        $todayDate = Carbon::today()->toDateString();
        if (Schema::hasColumn((new Reservation())->getTable(), 'reservation_date')) {
            $todayReservations = Reservation::whereDate('reservation_date', $todayDate)->get();
        } else {
            $todayReservations = Reservation::whereDate('created_at', $todayDate)->get();
        }

        // counts for the view/stats
        $stats = [
            'total_reservations'   => $reservations->count(),
            'pending_reservations' => $pendingReservations->count(),
            'total_customers'      => $totalCustomers,
            'total_restaurants'    => $totalRestaurants,
            'today_count'          => $todayReservations->count(),
            'pending_count'        => $pendingReservations->count(),
        ];

        if (view()->exists('staff.dashboard')) {
            return view('staff.dashboard', compact(
                'stats',
                'reservations',
                'pendingReservations',
                'todayReservations',
                'totalCustomers',
                'totalRestaurants'
            ));
        }

        return response()->json([
            'stats' => $stats,
            'reservations' => $reservations,
            'pending_reservations' => $pendingReservations,
            'today_reservations' => $todayReservations,
        ]);
    }
}
