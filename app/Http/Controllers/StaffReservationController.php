<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaffReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['customer', 'restaurant', 'table']);

        // Filter by restaurant if provided
        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Use pagination so appends() is available in views (avoid Collection::appends error)
        $reservations = $query->orderBy('reservation_date', 'desc')
                              ->orderBy('reservation_time', 'desc')
                              ->paginate(15);
        
        // Preserve current filters on pagination links
        $reservations->appends($request->except('page'));

        $restaurants = Restaurant::all();

        return view('staff.reservations', compact('reservations', 'restaurants'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $restaurants = Restaurant::all();
        return view('staff.reservations.create', compact('restaurants'));
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::create([
            'restaurant_id' => $request->restaurant_id,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'guests' => $request->guests,
        ]);

        return back()->with('success', 'Reservation created.');
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['customer', 'restaurant', 'table']);
        return view('staff.reservation-details', compact('reservation'));
    }

    /**
     * Update the reservation status.
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,confirmed,cancelled,completed'
        ]);

        $oldStatus = $reservation->status;
        // Prevent direct staff confirmation; convert to approved and notify customer
        $requestedStatus = $request->status;
        $newStatus = $requestedStatus === 'confirmed' ? 'approved' : $requestedStatus;
        $reservation->update(['status' => $newStatus]);

        // If manually set to approved, send email (only if customer/email exist)
        $customerEmail = optional($reservation->customer)->email;
        if ($newStatus === 'approved' && $oldStatus !== 'approved' && $customerEmail) {
            try {
                Mail::to($customerEmail)->send(new \App\Mail\ReservationApproved($reservation));
            } catch (\Exception $e) {
                return back()->with('warning', 'Reservation status updated, but email notification failed.');
            }
        }

        // Free table if cancelled or completed
        if (in_array($newStatus, ['cancelled', 'completed']) && $reservation->table) {
            $reservation->table->update(['status' => 'available']);
        }
        
        if ($requestedStatus === 'confirmed') {
            return back()->with('success', 'Reservation set to Approved. Awaiting customer confirmation.');
        }
        
        return back()->with('success', 'Reservation status updated.');
    }

    /**
     * Approve a pending reservation and notify customer.
     */
    public function approve(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be approved.');
        }

        $reservation->update(['status' => 'approved']);

        $customerEmail = optional($reservation->customer)->email;
        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new \App\Mail\ReservationApproved($reservation));
                return back()->with('success', 'Reservation approved. Customer will receive a confirmation email.');
            } catch (\Exception $e) {
                return back()->with('warning', 'Reservation approved, but email notification failed.');
            }
        }

        return back()->with('success', 'Reservation approved. No customer email on record to notify.');
    }

    /**
     * Mark a confirmed reservation as completed.
     */
    public function complete(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be completed.');
        }

        $reservation->update(['status' => 'completed']);

        // Free the table if assigned
        if ($reservation->table) {
            $reservation->table->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation marked as completed.');
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Reservation $reservation)
    {
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        $reservation->update(['status' => 'cancelled']);

        // Free the table if assigned
        if ($reservation->table) {
            $reservation->table->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation cancelled.');
    }
}