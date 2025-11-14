<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Table;
use Illuminate\Console\Command;

class CreateMultipleReservations extends Command
{
    protected $signature = 'create:multiple-reservations';
    protected $description = 'Create multiple test reservations for today';

    public function handle()
    {
        $this->info('Creating multiple test reservations for today...');
        
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
        
        // Get some tables
        $tables = Table::where('restaurant_id', $restaurant->id)->take(3)->get();
        
        $reservations = [
            [
                'time' => '12:00:00',
                'guests' => 2,
                'table_id' => $tables->first()?->id, // Assign to first table
                'status' => 'confirmed',
                'description' => 'Lunch reservation for 2 people'
            ],
            [
                'time' => '13:30:00',
                'guests' => 4,
                'table_id' => null, // No table assigned yet
                'status' => 'pending',
                'description' => 'Family lunch - needs table assignment'
            ],
            [
                'time' => '19:00:00',
                'guests' => 6,
                'table_id' => $tables->skip(1)->first()?->id, // Assign to second table
                'status' => 'confirmed',
                'description' => 'Dinner reservation for 6 people'
            ],
            [
                'time' => '20:30:00',
                'guests' => 2,
                'table_id' => null, // No table assigned yet
                'status' => 'pending',
                'description' => 'Romantic dinner - needs table assignment'
            ]
        ];
        
        foreach ($reservations as $index => $reservationData) {
            $reservation = Reservation::create([
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'table_id' => $reservationData['table_id'],
                'reservation_reference' => 'DEMO-' . (time() + $index),
                'reservation_date' => now()->toDateString(),
                'reservation_time' => $reservationData['time'],
                'number_of_guests' => $reservationData['guests'],
                'status' => $reservationData['status'],
                'special_requests' => $reservationData['description'],
                'customer_phone' => '09123456789',
                'customer_email' => $customer->email,
            ]);
            
            $tableInfo = $reservationData['table_id'] ? 
                "Table: " . $tables->where('id', $reservationData['table_id'])->first()?->table_name : 
                "No table assigned";
            
            $this->info("✓ Created reservation {$reservation->id}:");
            $this->line("  Time: {$reservationData['time']}");
            $this->line("  Guests: {$reservationData['guests']}");
            $this->line("  Status: {$reservationData['status']}");
            $this->line("  {$tableInfo}");
            $this->line("  Description: {$reservationData['description']}");
            $this->line("");
        }
        
        $this->info('All test reservations created successfully!');
        $this->info('Now you can test the seating management with real data.');
        
        return 0;
    }
}