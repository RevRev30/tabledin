<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;

class CheckUserRestaurants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-restaurants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user-restaurant relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking user-restaurant relationships...');
        
        $users = User::where('role', 'staff')->get();
        $this->info("Found {$users->count()} staff users:");
        
        foreach ($users as $user) {
            $restaurant = Restaurant::where('user_id', $user->id)->first();
            $this->line("- {$user->name} ({$user->email}) - Restaurant: " . ($restaurant ? $restaurant->name : 'None'));
        }
        
        $this->newLine();
        $this->info('All restaurants:');
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $user = User::find($restaurant->user_id);
            $this->line("- {$restaurant->name} - User: " . ($user ? $user->name . ' (' . $user->email . ')' : 'None'));
        }
        
        return 0;
    }
}