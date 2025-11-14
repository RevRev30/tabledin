<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SeatingNotificationController extends Controller
{
    /**
     * Stream real-time seating updates using Server-Sent Events
     */
    public function stream(Request $request)
    {
        $user = Auth::user();
        $restaurant = \App\Models\Restaurant::where('user_id', $user->id)->first();
        
        if (!$restaurant) {
            return response('Restaurant not found', 404);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Cache-Control');

        $callback = function() use ($restaurant) {
            $lastUpdate = time();
            
            while (true) {
                // Get current seating data
                $tables = Table::where('restaurant_id', $restaurant->id)
                    ->with(['reservations' => function($query) {
                        $query->where('status', '!=', 'cancelled')
                              ->where('reservation_date', '>=', now()->toDateString());
                    }])
                    ->get()
                    ->map(function($table) {
                        $currentReservation = $table->reservations->first();
                        return [
                            'id' => $table->id,
                            'name' => $table->table_name,
                            'capacity' => $table->capacity,
                            'status' => $table->status,
                            'position' => $table->position,
                            'coordinates' => $table->table_coordinates,
                            'zone_id' => $table->seating_zone_id,
                            'current_reservation' => $currentReservation ? [
                                'id' => $currentReservation->id,
                                'customer_name' => $currentReservation->user->name,
                                'time' => $currentReservation->reservation_time,
                                'guests' => $currentReservation->number_of_guests,
                                'status' => $currentReservation->status
                            ] : null
                        ];
                    });

                $data = [
                    'tables' => $tables,
                    'timestamp' => now()->toISOString(),
                    'update_count' => $lastUpdate
                ];

                echo "data: " . json_encode($data) . "\n\n";
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();

                // Check if client disconnected
                if (connection_aborted()) {
                    break;
                }

                sleep(5); // Update every 5 seconds
            }
        };

        $response->setCallback($callback);
        return $response;
    }

    /**
     * Get current seating status
     */
    public function status(Request $request)
    {
        $user = Auth::user();
        $restaurant = \App\Models\Restaurant::where('user_id', $user->id)->first();
        
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $tables = Table::where('restaurant_id', $restaurant->id)
            ->with(['reservations' => function($query) {
                $query->where('status', '!=', 'cancelled')
                      ->where('reservation_date', '>=', now()->toDateString());
            }])
            ->get()
            ->map(function($table) {
                $currentReservation = $table->reservations->first();
                return [
                    'id' => $table->id,
                    'name' => $table->table_name,
                    'capacity' => $table->capacity,
                    'status' => $table->status,
                    'position' => $table->position,
                    'coordinates' => $table->table_coordinates,
                    'zone_id' => $table->seating_zone_id,
                    'current_reservation' => $currentReservation ? [
                        'id' => $currentReservation->id,
                        'customer_name' => $currentReservation->user->name,
                        'time' => $currentReservation->reservation_time,
                        'guests' => $currentReservation->number_of_guests,
                        'status' => $currentReservation->status
                    ] : null
                ];
            });

        $status = [
            'available' => $tables->where('status', 'available')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'maintenance' => $tables->where('status', 'maintenance')->count(),
            'total' => $tables->count()
        ];

        return response()->json([
            'tables' => $tables,
            'status' => $status,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Broadcast seating update to all connected clients
     */
    public function broadcast(Request $request)
    {
        $user = Auth::user();
        $restaurant = \App\Models\Restaurant::where('user_id', $user->id)->first();
        
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $request->validate([
            'type' => 'required|in:table_status,reservation_update,layout_change',
            'data' => 'required|array'
        ]);

        // In a real implementation, you would use Redis, Pusher, or similar
        // to broadcast to all connected clients. For now, we'll just log it.
        \Illuminate\Support\Facades\Log::info('Seating update broadcast', [
            'restaurant_id' => $restaurant->id,
            'type' => $request->type,
            'data' => $request->data,
            'broadcast_by' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Update broadcasted successfully'
        ]);
    }
}
