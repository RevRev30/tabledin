<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TestReassignRoute extends Command
{
    protected $signature = 'test:reassign-route';
    protected $description = 'Test the reassign route directly via HTTP';

    public function handle()
    {
        $this->info('Testing reassign route via HTTP...');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        Auth::login($staffUser);
        $this->info('Logged in as: ' . $staffUser->name);
        
        // Get a reservation that has a table assigned
        $reservation = Reservation::whereNotNull('table_id')
            ->where('reservation_date', now()->toDateString())
            ->first();
            
        if (!$reservation) {
            $this->error('No reservations with assigned tables found for today');
            return 1;
        }
        
        $this->info("Found reservation: {$reservation->id} - {$reservation->customer?->name}");
        $this->info("Current table: {$reservation->table?->table_name}");
        
        // Get a different available table
        $newTable = Table::where('restaurant_id', $reservation->restaurant_id)
            ->where('id', '!=', $reservation->table_id)
            ->where('status', 'available')
            ->first();
            
        if (!$newTable) {
            $this->error('No other available tables found');
            return 1;
        }
        
        $this->info("Found new table: {$newTable->id} - {$newTable->table_name}");
        
        // Test the route via HTTP
        try {
            $url = "http://localhost/wtg/newproject/public/staff/seating/reservations/{$reservation->id}/reassign";
            $this->info("Testing URL: {$url}");
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, [
                'new_table_id' => $newTable->id
            ]);
            
            $this->info('Response status: ' . $response->status());
            $this->info('Response body: ' . $response->body());
            
            if ($response->successful()) {
                $this->info('✓ Route is accessible and working');
            } else {
                $this->error('✗ Route returned status: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error testing route: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}