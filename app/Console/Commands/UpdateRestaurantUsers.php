<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateRestaurantUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:update-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update restaurants with user_id for staff users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $staffUsers = User::where('role', 'staff')->get();
        
        if ($staffUsers->isEmpty()) {
            $this->error('No staff users found. Please create staff users first.');
            return 1;
        }

        $restaurants = Restaurant::whereNull('user_id')->get();
        
        if ($restaurants->isEmpty()) {
            $this->info('All restaurants already have user_id assigned.');
            return 0;
        }

        foreach ($restaurants as $index => $restaurant) {
            $user = $staffUsers->get($index % $staffUsers->count());
            $restaurant->update(['user_id' => $user->id]);
            $this->info("Updated {$restaurant->name} with user {$user->name} (ID: {$user->id})");
        }

        $this->info('All restaurants updated successfully!');
        return 0;
    }
}