<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestTodayReservations extends Command
{
    protected $signature = 'test:today-reservations';
    protected $description = 'Test today\'s reservations display';

    public function handle()
    {
        $this->info('=== TESTING TODAY\'S RESERVATIONS ===');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        if (!$staffUser) {
            $this->error('No staff user found');
            return 1;
        }
        
        Auth::login($staffUser);
        $this->info('Logged in as: ' . $staffUser->name);
        
        // Get restaurant
        $restaurant = Restaurant::where('user_id', $staffUser->id)->first();
        if (!$restaurant) {
            $restaurant = Restaurant::first();
        }
        
        $this->info('Using restaurant: ' . $restaurant->name);
        
        // Get today's reservations exactly like the controller does
        $todayReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'table'])
            ->orderBy('reservation_time')
            ->get();
            
        $this->info('Today\'s reservations count: ' . $todayReservations->count());
        
        foreach ($todayReservations as $reservation) {
            $this->line("Reservation ID: {$reservation->id}");
            $this->line("  Customer: " . ($reservation->customer ? $reservation->customer->name : 'NULL'));
            $this->line("  Time: " . ($reservation->reservation_time ? $reservation->reservation_time : 'NULL'));
            $this->line("  Table: " . ($reservation->table ? $reservation->table->table_name : 'NULL'));
            $this->line("  Status: {$reservation->status}");
            $this->line("  Guests: {$reservation->number_of_guests}");
            $this->line("---");
        }
        
        // Get all required variables like the controller does
        $layout = \App\Models\SeatingLayout::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->first();
            
        $tables = \App\Models\Table::where('restaurant_id', $restaurant->id)
            ->with(['reservations' => function($query) {
                $query->where('status', '!=', 'cancelled')
                      ->where('reservation_date', '>=', now()->toDateString())
                      ->orderBy('reservation_time');
            }])
            ->get();
            
        $zones = \App\Models\SeatingZone::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->get();
        
        // Test the view rendering
        try {
            $this->info('Testing view rendering...');
            $view = view('staff.seating.index', compact('restaurant', 'layout', 'tables', 'todayReservations', 'zones'));
            $html = $view->render();
            $this->info('✓ View rendered successfully');
            $this->info('HTML length: ' . strlen($html) . ' characters');
        } catch (\Exception $e) {
            $this->error('✗ View rendering failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}