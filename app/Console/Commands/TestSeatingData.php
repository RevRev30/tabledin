<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use App\Http\Controllers\SeatingController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestSeatingData extends Command
{
    protected $signature = 'test:seating-data';
    protected $description = 'Test the getSeatingData method';

    public function handle()
    {
        $this->info('Testing getSeatingData method...');
        
        // Login as staff user
        $staffUser = User::where('role', 'staff')->first();
        Auth::login($staffUser);
        
        // Create a request
        $request = \Illuminate\Http\Request::create('/staff/seating/data', 'GET');
        
        // Test the controller method
        $controller = new SeatingController();
        
        try {
            $response = $controller->getSeatingData($request);
            $this->info('✓ getSeatingData method executed successfully');
            
            $data = json_decode($response->getContent(), true);
            $this->info('Response data:');
            $this->line('  Tables count: ' . count($data['tables']));
            
            foreach ($data['tables'] as $table) {
                $this->line("  Table: {$table['name']} (ID: {$table['id']})");
                if ($table['current_reservation']) {
                    $this->line("    Reservation: {$table['current_reservation']['customer_name']}");
                } else {
                    $this->line("    No reservation");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error in getSeatingData: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}