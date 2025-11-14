<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\SeatingLayout;
use App\Models\SeatingZone;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SeatingController extends Controller
{
    /**
     * Resolve which restaurant to operate on (supports multi-restaurant)
     */
    private function resolveRestaurant(Request $request)
    {
        $requestedId = (int) $request->query('restaurant_id', 0);
        if ($requestedId > 0) {
            $restaurant = Restaurant::find($requestedId);
            if ($restaurant) {
                return $restaurant;
            }
        }

        $user = Auth::user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        if ($restaurant) {
            return $restaurant;
        }

        return Restaurant::first();
    }
    /**
     * Compute table status based on reservations
     */
    private function computeTableStatus($table)
    {
        $currentReservation = $table->reservations->first();
        
        if ($currentReservation) {
            // If there's a reservation, update status based on reservation status
            if ($currentReservation->status === 'confirmed') {
                return 'reserved';
            } elseif ($currentReservation->status === 'pending') {
                return 'pending';
            }
        }
        
        return $table->status;
    }
    /**
     * Display the seating management dashboard
     */
    public function index(Request $request)
    {
        $restaurant = $this->resolveRestaurant($request);
        if (!$restaurant) {
            return redirect()->route('staff.dashboard')->with('error', 'No restaurants found in the system.');
        }

        // Selected date (defaults to today)
        $selectedDate = $request->query('date');
        try {
            $selectedDate = $selectedDate ? Carbon::parse($selectedDate)->toDateString() : now()->toDateString();
        } catch (\Exception $e) {
            $selectedDate = now()->toDateString();
        }

        // Get active seating layout
        $layout = SeatingLayout::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->first();

        // Get all tables with their current status and reservations
        $tables = Table::where('restaurant_id', $restaurant->id)
            ->with(['reservations' => function($query) use ($selectedDate) {
                $query->where('status', '!=', 'cancelled')
                      ->where('reservation_date', $selectedDate)
                      ->orderBy('reservation_time');
            }])
            ->get();

        // Get current reservations for today
        $todayReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'table'])
            ->orderBy('reservation_time')
            ->get();

        // Get seating zones
        $zones = SeatingZone::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->get();

        $restaurants = Restaurant::orderBy('name')->get();
        return view('staff.seating.index', compact('restaurant', 'layout', 'tables', 'todayReservations', 'zones', 'restaurants', 'selectedDate'));
    }

    /**
     * Display the advanced seating management dashboard
     */
    public function advanced(Request $request)
    {
        $restaurant = $this->resolveRestaurant($request);
        if (!$restaurant) {
            return redirect()->route('staff.dashboard')->with('error', 'No restaurants found in the system.');
        }

        // Get active seating layout
        $layout = SeatingLayout::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->first();

        // Get all tables with their current status and reservations
        // Selected date (defaults to today)
        $selectedDate = $request->query('date');
        try {
            $selectedDate = $selectedDate ? Carbon::parse($selectedDate)->toDateString() : now()->toDateString();
        } catch (\Exception $e) {
            $selectedDate = now()->toDateString();
        }

        $tables = Table::where('restaurant_id', $restaurant->id)
            ->with(['reservations' => function($query) use ($selectedDate) {
                $query->where('status', '!=', 'cancelled')
                      ->where('reservation_date', $selectedDate)
                      ->orderBy('reservation_time');
            }])
            ->get();

        // Get current reservations for today
        $todayReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'table'])
            ->orderBy('reservation_time')
            ->get();

        // Get seating zones
        $zones = SeatingZone::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->get();

        $restaurants = Restaurant::orderBy('name')->get();
        return view('staff.seating.advanced', compact('restaurant', 'layout', 'tables', 'todayReservations', 'zones', 'restaurants', 'selectedDate'));
    }

    /**
     * Get real-time seating data for AJAX requests
     */
    public function getSeatingData(Request $request)
    {
        $restaurant = $this->resolveRestaurant($request);
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurants found'], 404);
        }

        // Selected date (defaults to today)
        $selectedDate = $request->query('date');
        try {
            $selectedDate = $selectedDate ? Carbon::parse($selectedDate)->toDateString() : now()->toDateString();
        } catch (\Exception $e) {
            $selectedDate = now()->toDateString();
        }

        // Get tables with current status and today's reservations
        $tables = Table::where('restaurant_id', $restaurant->id)
            ->with(['reservations' => function($query) use ($selectedDate) {
                $query->where('status', '!=', 'cancelled')
                      ->where('reservation_date', $selectedDate);
            }])
            ->get()
            ->map(function($table) use ($selectedDate) {
                $currentReservation = $table->reservations->first();
                
                // Determine table status based on reservations
                // Default: if not the selected day, don't carry over reserved/occupied from other days
                $today = now()->toDateString();
                if ($currentReservation) {
                    // If there's a reservation for the selected date
                    if ($currentReservation->status === 'confirmed') {
                        $tableStatus = 'reserved';
                    } elseif ($currentReservation->status === 'pending') {
                        $tableStatus = 'pending';
                    } else {
                        $tableStatus = $table->status === 'maintenance' ? 'maintenance' : 'available';
                    }
                } else {
                    if ($selectedDate === $today) {
                        // For today, keep real-time statuses (occupied/reserved) if present; otherwise available
                        $tableStatus = in_array($table->status, ['occupied', 'reserved']) ? $table->status : ($table->status === 'maintenance' ? 'maintenance' : 'available');
                    } else {
                        // For other dates, only maintenance carries over
                        $tableStatus = $table->status === 'maintenance' ? 'maintenance' : 'available';
                    }
                }
                
                return [
                    'id' => $table->id,
                    'name' => $table->table_name,
                    'capacity' => $table->capacity,
                    'status' => $tableStatus,
                    'position' => $table->position,
                    'coordinates' => $table->table_coordinates,
                    'zone_id' => $table->seating_zone_id,
                    'current_reservation' => $currentReservation ? [
                        'id' => $currentReservation->id,
                        'customer_name' => $currentReservation->customer?->name ?? 'Unknown User',
                        'time' => $currentReservation->reservation_time,
                        'guests' => $currentReservation->number_of_guests,
                        'status' => $currentReservation->status
                    ] : null
                ];
            });

        return response()->json([
            'tables' => $tables,
            'timestamp' => now()->toISOString(),
            'restaurant_id' => $restaurant->id,
            'date' => $selectedDate
        ]);
    }

    /**
     * Update table status (available, occupied, reserved, maintenance)
     */
    public function updateTableStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $oldStatus = $table->status;
        $table->update(['status' => $request->status]);

        // Log the status change
        Log::info('Table status updated', [
            'table_id' => $table->id,
            'table_name' => $table->table_name,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Table {$table->table_name} status updated to {$request->status}",
            'table' => [
                'id' => $table->id,
                'name' => $table->table_name,
                'status' => $table->status
            ]
        ]);
    }

    /**
     * Assign table to a reservation
     */
    public function assignTable(Request $request, Reservation $reservation)
    {
        // Disallow assignment for completed or cancelled reservations
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign a table to a reservation that is ' . $reservation->status . '.'
            ], 400);
        }
        $request->validate([
            'table_id' => 'required|exists:tables,id'
        ]);

        $table = Table::findOrFail($request->table_id);

        // Check if table is available for the reservation time
        $conflictingReservation = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $reservation->reservation_date)
            ->where('reservation_time', $reservation->reservation_time)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $reservation->id)
            ->first();

        if ($conflictingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Table is already reserved for this time slot'
            ], 400);
        }

        DB::transaction(function() use ($reservation, $table) {
            // Update reservation
            $reservation->update([
                'table_id' => $table->id,
                'status' => 'approved'
            ]);

            // Update table status
            $table->update(['status' => 'reserved']);

            // Log the assignment
            Log::info('Table assigned to reservation', [
                'reservation_id' => $reservation->id,
                'table_id' => $table->id,
                'table_name' => $table->table_name,
                'assigned_by' => Auth::id()
            ]);
        });

        // Send notification to customer (with confirm/cancel links)
        try {
            if ($reservation->customer && $reservation->customer->email) {
                Mail::to($reservation->customer->email)->send(
                    new \App\Mail\ReservationTableUpdated($reservation->fresh(['customer','restaurant','table']), null, $table->table_name)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send table assignment email', ['reservation_id' => $reservation->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Table {$table->table_name} assigned to reservation",
            'reservation' => $reservation->fresh(['customer', 'table'])
        ]);
    }

    /**
     * Reassign table for a reservation
     */
    public function reassignTable(Request $request, Reservation $reservation)
    {
        // Disallow reassignment for completed or cancelled reservations
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reassign a table for a reservation that is ' . $reservation->status . '.'
            ], 400);
        }
        $request->validate([
            'new_table_id' => 'required|exists:tables,id'
        ]);

        $oldTable = $reservation->table;
        $newTable = Table::findOrFail($request->new_table_id);

        // Check if new table is available
        $conflictingReservation = Reservation::where('table_id', $newTable->id)
            ->where('reservation_date', $reservation->reservation_date)
            ->where('reservation_time', $reservation->reservation_time)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $reservation->id)
            ->first();

        if ($conflictingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'New table is already reserved for this time slot'
            ], 400);
        }

        DB::transaction(function() use ($reservation, $oldTable, $newTable) {
            // Update reservation
            $reservation->update(['table_id' => $newTable->id]);

            // Update old table status
            if ($oldTable) {
                $oldTable->update(['status' => 'available']);
            }

            // Update new table status
            $newTable->update(['status' => 'reserved']);

            // Log the reassignment
            Log::info('Table reassigned for reservation', [
                'reservation_id' => $reservation->id,
                'old_table_id' => $oldTable ? $oldTable->id : null,
                'old_table_name' => $oldTable ? $oldTable->table_name : null,
                'new_table_id' => $newTable->id,
                'new_table_name' => $newTable->table_name,
                'reassigned_by' => Auth::id()
            ]);
        });

        // Send notification to customer (with confirm/cancel links)
        try {
            if ($reservation->customer && $reservation->customer->email) {
                Mail::to($reservation->customer->email)->send(
                    new \App\Mail\ReservationTableUpdated($reservation->fresh(['customer','restaurant','table']), $oldTable?->table_name, $newTable->table_name)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send table reassignment email', ['reservation_id' => $reservation->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Reservation reassigned from {$oldTable?->table_name} to {$newTable->table_name}",
            'reservation' => $reservation->fresh(['customer', 'table'])
        ]);
    }

    /**
     * Get available tables for a specific time slot
     */
    public function getAvailableTables(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        $restaurant = $this->resolveRestaurant($request);

        // Get tables that are not reserved for the specified time
        $reservedTableIds = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', $request->date)
            ->where('reservation_time', $request->time)
            ->where('status', '!=', 'cancelled')
            ->pluck('table_id');

        $availableTables = Table::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->whereNotIn('id', $reservedTableIds)
            ->where('status', '!=', 'maintenance')
            ->get()
            ->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->table_name,
                    'capacity' => $table->capacity,
                    'zone' => $table->seatingZone?->zone_name,
                    'position' => $table->position
                ];
            });

        return response()->json(['tables' => $availableTables]);
    }

    /**
     * Create or update seating layout
     */
    public function updateLayout(Request $request)
    {
        $restaurant = $this->resolveRestaurant($request);

        $request->validate([
            'layout_name' => 'required|string|max:255',
            'width' => 'required|integer|min:400|max:2000',
            'height' => 'required|integer|min:300|max:1500',
            'layout_data' => 'required|array'
        ]);

        $layout = SeatingLayout::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'is_active' => true],
            [
                'layout_name' => $request->layout_name,
                'width' => $request->width,
                'height' => $request->height,
                'layout_data' => $request->layout_data,
                'is_default' => true
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Seating layout updated successfully',
            'layout' => $layout
        ]);
    }

    /**
     * Update table position on the layout
     */
    public function updateTablePosition(Request $request, Table $table)
    {
        $request->validate([
            'position' => 'required|array',
            'position.x' => 'required|numeric',
            'position.y' => 'required|numeric',
            'coordinates' => 'nullable|array'
        ]);

        $table->update([
            'position' => $request->position,
            'table_coordinates' => $request->coordinates
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table position updated',
            'table' => [
                'id' => $table->id,
                'position' => $table->position,
                'coordinates' => $table->table_coordinates
            ]
        ]);
    }

    /**
     * Send notification to customer about table change
     */
    private function notifyCustomerOfTableChange(Reservation $reservation, ?Table $oldTable = null, ?Table $newTable = null)
    {
        try {
            $customer = $reservation->customer;
            $restaurant = $reservation->restaurant;
            
            $subject = 'Table Assignment Update - ' . $restaurant->name;
            
            if ($oldTable && $newTable) {
                // Table reassignment
                $message = "Your table has been reassigned from {$oldTable->table_name} to {$newTable->table_name}.";
                $message .= "\n\nNew table details:";
                $message .= "\n- Table: {$newTable->table_name}";
                $message .= "\n- Capacity: {$newTable->capacity} guests";
                $message .= "\n- Location: {$newTable->location}";
            } else if ($newTable) {
                // New table assignment
                $message = "Your table has been assigned: {$newTable->table_name}.";
                $message .= "\n\nTable details:";
                $message .= "\n- Table: {$newTable->table_name}";
                $message .= "\n- Capacity: {$newTable->capacity} guests";
                $message .= "\n- Location: {$newTable->location}";
            }
            
            $message .= "\n\nReservation details:";
            $message .= "\n- Date: " . Carbon::parse($reservation->reservation_date)->format('M d, Y');
            $message .= "\n- Time: " . \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A');
            $message .= "\n- Guests: {$reservation->number_of_guests}";
            $message .= "\n\nThank you for choosing {$restaurant->name}!";
            
            // Send email notification
            Mail::raw($message, function ($mail) use ($customer, $subject, $restaurant) {
                $mail->to($customer->email, $customer->name)
                     ->subject($subject)
                     ->from(config('mail.from.address'), $restaurant->name);
            });
            
            // Log the notification
            Log::info('Table change notification sent', [
                'reservation_id' => $reservation->id,
                'customer_email' => $customer->email,
                'old_table' => $oldTable?->table_name,
                'new_table' => $newTable?->table_name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send table change notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to customer about reservation status change
     */
    private function notifyCustomerOfStatusChange(Reservation $reservation, string $oldStatus, string $newStatus)
    {
        try {
            $customer = $reservation->customer;
            $restaurant = $reservation->restaurant;
            
            $subject = 'Reservation Update - ' . $restaurant->name;
            
            $message = "Your reservation status has been updated from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . ".";
            
            $message .= "\n\nReservation details:";
            $message .= "\n- Date: " . Carbon::parse($reservation->reservation_date)->format('M d, Y');
            $message .= "\n- Time: " . \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A');
            $message .= "\n- Guests: {$reservation->number_of_guests}";
            
            if ($reservation->table) {
                $message .= "\n- Table: {$reservation->table->table_name}";
            }
            
            $message .= "\n\nThank you for choosing {$restaurant->name}!";
            
            // Send email notification
            Mail::raw($message, function ($mail) use ($customer, $subject, $restaurant) {
                $mail->to($customer->email, $customer->name)
                     ->subject($subject)
                     ->from(config('mail.from.address'), $restaurant->name);
            });
            
            // Log the notification
            Log::info('Status change notification sent', [
                'reservation_id' => $reservation->id,
                'customer_email' => $customer->email,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send status change notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
