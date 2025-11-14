<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestAllViews extends Command
{
    protected $signature = 'test:all-views';
    protected $description = 'Test all views that might have table_name issues';

    public function handle()
    {
        $this->info('Testing all views for table_name issues...');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        Auth::login($staffUser);
        
        // Get test data
        $restaurant = Restaurant::first();
        $restaurants = Restaurant::where('is_active', true)->get();
        $reservations = Reservation::with(['customer', 'table'])->get();
        $todayReservations = Reservation::where('reservation_date', now()->toDateString())
            ->with(['customer', 'table'])
            ->get();
        
        $this->info('Found ' . $reservations->count() . ' reservations');
        $this->info('Found ' . $todayReservations->count() . ' today\'s reservations');
        
        // Test views that might have table_name issues
        $views = [
            'staff.seating.index' => compact('restaurant', 'todayReservations'),
            'staff.seating.advanced' => compact('restaurant', 'todayReservations'),
            'staff.reservations' => compact('reservations', 'restaurants'),
            'reservations.index' => compact('reservations'),
        ];
        
        foreach ($views as $viewName => $data) {
            try {
                $this->info("Testing view: {$viewName}");
                
                // Add missing variables for seating views
                if (str_contains($viewName, 'seating')) {
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
                    
                    $data = array_merge($data, compact('layout', 'tables', 'zones'));
                }
                
                $view = view($viewName, $data);
                $html = $view->render();
                $this->info("  ✓ {$viewName} rendered successfully");
                
            } catch (\Exception $e) {
                $this->error("  ✗ {$viewName} failed: " . $e->getMessage());
                return 1;
            }
        }
        
        $this->info('All views tested successfully!');
        return 0;
    }
}