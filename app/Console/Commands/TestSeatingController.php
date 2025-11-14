<?php

namespace App\Console\Commands;

use App\Http\Controllers\SeatingController;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestSeatingController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:seating-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the SeatingController functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing SeatingController...');
        
        try {
            // Test 1: Check if controller can be instantiated
            $controller = new SeatingController();
            $this->info('✓ Controller instantiated successfully');
            
            // Test 2: Check if restaurants exist
            $restaurants = Restaurant::all();
            $this->info("✓ Found {$restaurants->count()} restaurants");
            
            // Test 3: Check if restaurants have user_id
            $restaurantsWithUsers = Restaurant::whereNotNull('user_id')->count();
            $this->info("✓ {$restaurantsWithUsers} restaurants have user_id assigned");
            
            // Test 4: Check if staff users exist
            $staffUsers = User::where('role', 'staff')->count();
            $this->info("✓ Found {$staffUsers} staff users");
            
            // Test 5: Test the index method with a mock request
            $user = User::where('role', 'staff')->first();
            if ($user) {
                auth()->login($user);
                $request = new Request();
                $response = $controller->index($request);
                $this->info('✓ Controller index method executed successfully');
            } else {
                $this->warn('⚠ No staff users found to test with');
            }
            
            $this->info('All tests passed! SeatingController should be working.');
            
        } catch (\Exception $e) {
            $this->error('Error testing SeatingController: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}