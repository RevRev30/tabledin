<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReservationController extends Controller
{
    // ensure authorize() is available
    use AuthorizesRequests;

    /**
     * Show the form to create a reservation for a specific restaurant.
     */
    public function create(Restaurant $restaurant): View
    {
        // active tables for this restaurant
        $tables = $restaurant->tables()->where('is_active', true)->orderBy('table_name')->get();

        // simple time slots (every 30 minutes as example)
        $timeSlots = [];
        $startHour = 10; // adjust to your business hours
        $endHour = 21;
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            foreach ([0, 30] as $minute) {
                $timeSlots[] = sprintf('%02d:%02d', $hour, $minute);
            }
        }

        return view('reservations.create', compact('restaurant', 'tables', 'timeSlots'));
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|string',
            'number_of_guests' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $reservation = Reservation::create([
            'restaurant_id'     => $validated['restaurant_id'],
            // DB expects customer_id column — set the authenticated user as the customer
            'customer_id'       => Auth::id(),
            'reservation_date'  => $validated['reservation_date'],
            'reservation_time'  => $validated['reservation_time'],
            'number_of_guests'  => $validated['number_of_guests'],
            'table_id'          => $validated['table_id'] ?? null,
            'special_requests'  => $validated['special_requests'] ?? null,
            'status'            => 'pending',
        ]);

        return redirect()->route('reservations.index')->with('success', 'Reservation created.');
    }

    public function index(Request $request): View
    {
        $reservations = $request->user()->reservations()
            ->with(['restaurant', 'table'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();

        return view('reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation): View
    {
        $this->authorize('update', $reservation);
        
        $restaurant = $reservation->restaurant;
        $tables = $restaurant->tables()->where('is_active', true)->orderBy('table_name')->get();
        
        // Generate time slots
        $timeSlots = [];
        for ($hour = 10; $hour <= 22; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $timeSlots[] = $time;
            }
        }

        return view('reservations.edit', compact('reservation', 'restaurant', 'tables', 'timeSlots'));
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $validated = $request->validate([
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'table_id' => ['required', 'exists:tables,id'],
            'special_requests' => ['nullable', 'string'],
        ]);

        // Check availability (excluding current reservation)
        $existingReservation = Reservation::where('table_id', $validated['table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('reservation_time', $validated['reservation_time'])
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingReservation) {
            return back()->withErrors(['table_id' => 'This table is already booked for the selected date and time.']);
        }

        $table = Table::findOrFail($validated['table_id']);
        if ($table->capacity < $validated['number_of_guests']) {
            return back()->withErrors(['number_of_guests' => 'This table can only accommodate ' . $table->capacity . ' guests.']);
        }

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)->with('status', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')->with('status', 'Reservation cancelled successfully.');
    }

    /**
     * Customer confirms their reservation.
     */
    public function customerConfirm(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (in_array($reservation->status, ['cancelled', 'completed', 'confirmed'])) {
            return back()->with('error', 'This reservation cannot be confirmed.');
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        // Ensure the assigned table reflects reserved status
        if ($reservation->table) {
            $reservation->table->update(['status' => 'reserved']);
        }

        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation confirmed.');
    }

    /**
     * Customer cancels their reservation.
     */
    public function customerCancel(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Free assigned table if any
        if ($reservation->table) {
            $reservation->table->update(['status' => 'available']);
        }

        return redirect()->route('reservations.index')->with('success', 'Reservation cancelled.');
    }

    /**
     * Public signed confirmation without login.
     */
    public function publicConfirm(Request $request, Reservation $reservation)
    {
        if (! $request->hasValidSignature()) {
            return response('Invalid or expired link.', 403);
        }

        if (in_array($reservation->status, ['cancelled', 'completed', 'confirmed'])) {
            return view('reservations.public-status', [
                'title' => 'Reservation Status',
                'message' => 'This reservation cannot be confirmed.',
            ]);
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        // Ensure the assigned table reflects reserved status
        if ($reservation->table) {
            $reservation->table->update(['status' => 'reserved']);
        }

        return view('reservations.public-status', [
            'title' => 'Reservation Confirmed',
            'message' => 'Thank you! Your reservation is now confirmed.',
        ]);
    }

    /**
     * Public signed cancellation without login.
     */
    public function publicCancel(Request $request, Reservation $reservation)
    {
        if (! $request->hasValidSignature()) {
            return response('Invalid or expired link.', 403);
        }

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return view('reservations.public-status', [
                'title' => 'Reservation Status',
                'message' => 'This reservation cannot be cancelled.',
            ]);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        if ($reservation->table) {
            $reservation->table->update(['status' => 'available']);
        }

        return view('reservations.public-status', [
            'title' => 'Reservation Cancelled',
            'message' => 'Your reservation has been cancelled.',
        ]);
    }
}


