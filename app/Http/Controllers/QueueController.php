<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QueueController extends Controller
{
    /**
     * Show queue for a restaurant (JSON for sidebar widgets)
     */
    public function index(Request $request)
    {
        $restaurantId = (int) $request->query('restaurant_id');
        $restaurant = $restaurantId ? Restaurant::find($restaurantId) : null;
        if (! $restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $waiting = Queue::where('restaurant_id', $restaurant->id)->where('status', 'waiting')->orderBy('joined_at')->get();
        $called = Queue::where('restaurant_id', $restaurant->id)->where('status', 'called')->orderBy('called_at')->get();
        $current = $called->first();

        $estimateMinutes = $this->estimateWaitMinutes($waiting->count());

        return response()->json([
            'success' => true,
            'queue' => [
                'waiting_count' => $waiting->count(),
                'current_token' => $current?->token_number,
                'estimate_minutes' => $estimateMinutes,
                'current' => $current ? [
                    'id' => $current->id,
                    'token' => $current->token_number,
                    'name' => $current->customer_name,
                    'size' => $current->party_size,
                ] : null,
                'waiting' => $waiting->map(fn($q) => [
                    'id' => $q->id,
                    'token' => $q->token_number,
                    'name' => $q->customer_name,
                    'size' => $q->party_size,
                    'joined_at' => optional($q->joined_at)->toIso8601String(),
                ]),
            ]
        ]);
    }

    /**
     * Enqueue a walk-in
     */
    public function enqueue(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'party_size' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Next token is date-based simple counter: Q-YYYYMMDD-XXX
        $datePrefix = now()->format('Ymd');
        $todayCount = Queue::where('restaurant_id', $validated['restaurant_id'])
            ->whereDate('joined_at', now()->toDateString())
            ->count();
        $token = sprintf('Q-%s-%03d', $datePrefix, $todayCount + 1);

        $entry = Queue::create([
            'restaurant_id' => $validated['restaurant_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? '',
            'party_size' => $validated['party_size'],
            'token_number' => $token,
            'status' => 'waiting',
            'estimated_wait_time' => $this->estimateWaitMinutes($todayCount),
            'joined_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Added to queue', 'entry' => $entry]);
    }

    /** Call next waiting party */
    public function callNext(Request $request)
    {
        $restaurantId = (int) $request->input('restaurant_id');
        $next = Queue::where('restaurant_id', $restaurantId)->where('status', 'waiting')->orderBy('joined_at')->first();
        if (! $next) {
            return response()->json(['success' => false, 'message' => 'No waiting parties'], 400);
        }
        $next->update(['status' => 'called', 'called_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Called token ' . $next->token_number, 'entry' => $next]);
    }

    /** Mark called party as seated */
    public function markSeated(Request $request)
    {
        $id = (int) $request->input('id');
        $entry = Queue::find($id);
        if (! $entry) return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        $entry->update(['status' => 'seated', 'seated_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Marked as seated', 'entry' => $entry]);
    }

    /** Cancel a waiting entry */
    public function cancel(Request $request)
    {
        $id = (int) $request->input('id');
        $entry = Queue::find($id);
        if (! $entry) return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        $entry->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Cancelled', 'entry' => $entry]);
    }

    /**
     * Seat the current (or specified) walk-in to a table by creating a Reservation.
     */
    public function seatToTable(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:queue,id',
            'table_id' => 'required|integer|exists:tables,id',
        ]);

        $entry = Queue::findOrFail($validated['id']);
        $table = Table::findOrFail($validated['table_id']);

        if (!in_array($entry->status, ['called','waiting'])) {
            return response()->json(['success' => false, 'message' => 'Only waiting or called entries can be seated'], 400);
        }

        // Ensure table is available
        if (in_array($table->status, ['reserved','occupied','maintenance'])) {
            return response()->json(['success' => false, 'message' => 'Selected table is not available'], 400);
        }

        // Create or find a lightweight customer user for the walk-in
        $email = $entry->customer_phone ? ('walkin+' . preg_replace('/\D+/', '', $entry->customer_phone) . '@tabledin.local') : ('walkin+' . $entry->token_number . '@tabledin.local');
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $entry->customer_name,
                'password' => Hash::make(Str::random(12)),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create a minimal reservation for the walk-in
        $reservation = Reservation::create([
            'restaurant_id' => $entry->restaurant_id,
            'customer_id' => $user->id,
            'reservation_date' => now()->toDateString(),
            'reservation_time' => now()->format('H:i'),
            'number_of_guests' => max(1, (int) $entry->party_size),
            'table_id' => $table->id,
            'special_requests' => 'Walk-in ' . $entry->token_number,
            'status' => 'confirmed',
        ]);

        // Mark table as reserved and queue entry as seated
        $table->update(['status' => 'reserved']);
        $entry->update(['status' => 'seated', 'seated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Walk-in seated at ' . $table->table_name,
            'reservation_id' => $reservation->id,
        ]);
    }

    private function estimateWaitMinutes(int $waitingAhead): int
    {
        // Simple heuristic: 10 minutes per party ahead
        return max(0, $waitingAhead * 10);
    }
}


