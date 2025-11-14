<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TestCSRFToken extends Command
{
    protected $signature = 'test:csrf-token';
    protected $description = 'Test CSRF token functionality';

    public function handle()
    {
        $this->info('Testing CSRF token...');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        Auth::login($staffUser);
        $this->info('Logged in as: ' . $staffUser->name);
        
        try {
            // First, get the CSRF token from the login page
            $loginResponse = Http::get('http://localhost/wtg/newproject/public/login');
            $this->info('Login page status: ' . $loginResponse->status());
            
            // Extract CSRF token from the response
            $csrfToken = null;
            if (preg_match('/name="csrf-token" content="([^"]+)"/', $loginResponse->body(), $matches)) {
                $csrfToken = $matches[1];
                $this->info('CSRF token found: ' . substr($csrfToken, 0, 20) . '...');
            } else {
                $this->error('CSRF token not found in login page');
                return 1;
            }
            
            // Test the reassign route with CSRF token
            $reservation = \App\Models\Reservation::whereNotNull('table_id')
                ->where('reservation_date', now()->toDateString())
                ->first();
                
            if (!$reservation) {
                $this->error('No reservations with assigned tables found for today');
                return 1;
            }
            
            $newTable = \App\Models\Table::where('restaurant_id', $reservation->restaurant_id)
                ->where('id', '!=', $reservation->table_id)
                ->where('status', 'available')
                ->first();
                
            if (!$newTable) {
                $this->error('No other available tables found');
                return 1;
            }
            
            $url = "http://localhost/wtg/newproject/public/staff/seating/reservations/{$reservation->id}/reassign";
            $this->info("Testing URL: {$url}");
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-CSRF-TOKEN' => $csrfToken,
            ])->post($url, [
                'new_table_id' => $newTable->id
            ]);
            
            $this->info('Response status: ' . $response->status());
            $this->info('Response body: ' . $response->body());
            
            if ($response->successful()) {
                $this->info('✓ CSRF token is working correctly');
            } else {
                $this->error('✗ CSRF token test failed with status: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error testing CSRF token: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}