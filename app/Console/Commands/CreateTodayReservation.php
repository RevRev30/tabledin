<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTodayReservation extends Command
{
    protected $signature = 'create:today-reservation';
    protected $description = 'Create a test reservation for today';

    public function handle()
    {
        $this->info('Creating test reservation for today...');
        
        // Get a customer user
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $this->error('No customer users found. Please create a customer account first.');
            return 1;
        }
        
        // Get a restaurant
        $restaurant = Restaurant::first();
        if (!$restaurant) {
            $this->error('No restaurants found.');
            return 1;
        }
        
        // Create a reservation for today
        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'table_id' => null, // No table assigned yet - this is what we want to test
            'reservation_reference' => 'TEST-' . time(),
            'reservation_date' => now()->toDateString(),
            'reservation_time' => now()->addHours(2)->format('H:i:s'),
            'number_of_guests' => 4,
            'status' => 'confirmed',
            'special_requests' => 'Test reservation for seating management',
            'customer_phone' => '09123456789',
            'customer_email' => $customer->email,
        ]);
        
        $this->info('✓ Created reservation:');
        $this->line("  ID: {$reservation->id}");
        $this->line("  Customer: {$customer->name}");
        $this->line("  Restaurant: {$restaurant->name}");
        $this->line("  Date: {$reservation->reservation_date}");
        $this->line("  Time: {$reservation->reservation_time}");
        $this->line("  Guests: {$reservation->number_of_guests}");
        $this->line("  Status: {$reservation->status}");
        $this->line("  Table: " . ($reservation->table_id ? 'Assigned' : 'Not assigned'));
        
        $this->info('Now you can test the seating management with this reservation!');
        
        return 0;
    }
}