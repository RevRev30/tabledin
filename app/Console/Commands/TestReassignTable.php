<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Table;
use App\Http\Controllers\SeatingController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestReassignTable extends Command
{
    protected $signature = 'test:reassign-table';
    protected $description = 'Test the reassignTable method directly';

    public function handle()
    {
        $this->info('Testing reassignTable method...');
        
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
        
        // Test the controller method
        try {
            $controller = new SeatingController();
            $request = Request::create('/staff/seating/reservations/' . $reservation->id . '/reassign', 'POST', [
                'new_table_id' => $newTable->id
            ]);
            
            $this->info('Making request to reassignTable method...');
            $response = $controller->reassignTable($request, $reservation);
            
            $this->info('Response status: ' . $response->getStatusCode());
            $this->info('Response content: ' . $response->getContent());
            
            if ($response->getStatusCode() === 200) {
                $this->info('✓ reassignTable method executed successfully');
            } else {
                $this->error('✗ reassignTable method returned status: ' . $response->getStatusCode());
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error in reassignTable: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}