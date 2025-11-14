<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestSeatingRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:seating-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the seating management route';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing seating management route...');
        
        try {
            // Test 1: Check if route exists
            $route = Route::getRoutes()->getByName('staff.seating.index');
            if ($route) {
                $this->info('✓ Route staff.seating.index exists');
                $this->info('  URI: ' . $route->uri());
                $this->info('  Action: ' . $route->getActionName());
            } else {
                $this->error('✗ Route staff.seating.index not found');
                return 1;
            }
            
            // Test 2: Check middleware
            $middleware = $route->gatherMiddleware();
            $this->info('✓ Middleware: ' . implode(', ', $middleware));
            
            // Test 3: Test with a staff user
            $staffUser = User::where('role', 'staff')->first();
            if ($staffUser) {
                auth()->login($staffUser);
                $this->info('✓ Logged in as: ' . $staffUser->name . ' (' . $staffUser->email . ')');
                
                // Test 4: Make a request to the route
                $response = app('Illuminate\Http\Request')->create('/staff/seating', 'GET');
                $response = app('Illuminate\Routing\Router')->dispatch($response);
                
                if ($response->getStatusCode() === 200) {
                    $this->info('✓ Route returns 200 OK');
                } elseif ($response->getStatusCode() === 302) {
                    $this->warn('⚠ Route returns 302 redirect');
                    $this->info('  Redirect location: ' . $response->headers->get('Location'));
                } else {
                    $this->warn('⚠ Route returns status: ' . $response->getStatusCode());
                }
            } else {
                $this->warn('⚠ No staff users found');
            }
            
            $this->info('Route test completed!');
            
        } catch (\Exception $e) {
            $this->error('Error testing route: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}