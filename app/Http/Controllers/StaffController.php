<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaffController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes, no need to define here
    }

    /**
     * Display dashboard for staff/admin
     */
    public function dashboard(): View
    {
        $todayReservations = Reservation::with(['customer', 'restaurant', 'table'])
            ->whereDate('reservation_date', today())
            ->orderBy('reservation_time')
            ->get();

        $upcomingReservations = Reservation::with(['customer', 'restaurant', 'table'])
            ->whereDate('reservation_date', '>', today())
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(10)
            ->get();

        $pendingReservations = Reservation::with(['customer', 'restaurant', 'table'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $stats = [
            'today_count' => $todayReservations->count(),
            'pending_count' => $pendingReservations->count(),
            'total_restaurants' => Restaurant::where('is_active', true)->count(),
            'total_customers' => User::where('role', 'customer')->orWhereNull('role')->count(),
        ];

        return view('staff.dashboard', compact(
            'todayReservations',
            'upcomingReservations', 
            'pendingReservations',
            'stats'
        ));
    }

    /**
     * Display all reservations
     */
    public function reservations(Request $request): View
    {
        $query = Reservation::with(['customer', 'restaurant', 'table']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by restaurant
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->paginate(20);

        $restaurants = Restaurant::where('is_active', true)->get();

        return view('staff.reservations', compact('reservations', 'restaurants'));
    }

    /**
     * Display specific reservation details
     */
    public function showReservation(Reservation $reservation): View
    {
        $reservation->load(['customer', 'restaurant', 'table']);
        return view('staff.reservation-details', compact('reservation'));
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $reservation->update([
            'status' => $request->status,
            'staff_notes' => $request->staff_notes
        ]);

        return redirect()->back()->with('success', 'Reservation status updated successfully.');
    }

    /**
     * Display restaurant management
     */
    public function restaurants(): View
    {
        $restaurants = Restaurant::withCount(['tables', 'reservations'])
            ->orderBy('name')
            ->get();

        return view('staff.restaurants', compact('restaurants'));
    }

    /**
     * Display customer management
     */
    public function customers(Request $request): View
    {
        $query = User::where('role', 'customer')->orWhereNull('role');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->withCount('reservations')
            ->orderBy('name')
            ->paginate(20);

        return view('staff.customers', compact('customers'));
    }
}
