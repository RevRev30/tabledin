<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Table;
use App\Http\Controllers\SeatingController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestAssignTable extends Command
{
    protected $signature = 'test:assign-table';
    protected $description = 'Test the assignTable method directly';

    public function handle()
    {
        $this->info('Testing assignTable method...');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        Auth::login($staffUser);
        $this->info('Logged in as: ' . $staffUser->name);
        
        // Get a reservation without a table
        $reservation = Reservation::whereNull('table_id')
            ->where('reservation_date', now()->toDateString())
            ->first();
            
        if (!$reservation) {
            $this->error('No unassigned reservations found for today');
            return 1;
        }
        
        $this->info("Found reservation: {$reservation->id} - {$reservation->customer?->name}");
        
        // Get an available table
        $table = Table::where('restaurant_id', $reservation->restaurant_id)
            ->where('status', 'available')
            ->first();
            
        if (!$table) {
            $this->error('No available tables found');
            return 1;
        }
        
        $this->info("Found table: {$table->id} - {$table->table_name}");
        
        // Test the controller method
        try {
            $controller = new SeatingController();
            $request = Request::create('/staff/seating/reservations/' . $reservation->id . '/assign', 'POST', [
                'table_id' => $table->id
            ]);
            
            $this->info('Making request to assignTable method...');
            $response = $controller->assignTable($request, $reservation);
            
            $this->info('Response status: ' . $response->getStatusCode());
            $this->info('Response content: ' . $response->getContent());
            
            if ($response->getStatusCode() === 200) {
                $this->info('✓ assignTable method executed successfully');
            } else {
                $this->error('✗ assignTable method returned status: ' . $response->getStatusCode());
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error in assignTable: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}