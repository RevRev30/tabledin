<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Table;
use Illuminate\Console\Command;

class AnalyzeReservations extends Command
{
    protected $signature = 'analyze:reservations';
    protected $description = 'Analyze reservation data and relationships';

    public function handle()
    {
        $this->info('=== RESERVATION ANALYSIS ===');
        
        // 1. Check total reservations
        $totalReservations = Reservation::count();
        $this->info("Total reservations in database: {$totalReservations}");
        
        if ($totalReservations === 0) {
            $this->warn('⚠️  NO RESERVATIONS FOUND! This is why you see no reservations in seating management.');
            $this->info('You need to create some test reservations first.');
            return 0;
        }
        
        // 2. Check reservations by restaurant
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $reservations = Reservation::where('restaurant_id', $restaurant->id)->count();
            $this->info("Restaurant '{$restaurant->name}': {$reservations} reservations");
        }
        
        // 3. Check today's reservations
        $todayReservations = Reservation::where('reservation_date', now()->toDateString())->count();
        $this->info("Today's reservations: {$todayReservations}");
        
        // 4. Check reservations with/without tables
        $withTables = Reservation::whereNotNull('table_id')->count();
        $withoutTables = Reservation::whereNull('table_id')->count();
        $this->info("Reservations with tables assigned: {$withTables}");
        $this->info("Reservations without tables: {$withoutTables}");
        
        // 5. Check customer relationships
        $reservationsWithCustomers = Reservation::whereHas('customer')->count();
        $reservationsWithoutCustomers = Reservation::whereDoesntHave('customer')->count();
        $this->info("Reservations with customer data: {$reservationsWithCustomers}");
        $this->info("Reservations without customer data: {$reservationsWithoutCustomers}");
        
        // 6. Show sample reservation data
        $this->newLine();
        $this->info('=== SAMPLE RESERVATION DATA ===');
        $sampleReservations = Reservation::with(['customer', 'restaurant', 'table'])
            ->limit(5)
            ->get();
            
        foreach ($sampleReservations as $reservation) {
            $this->line("ID: {$reservation->id}");
            $this->line("  Date: {$reservation->reservation_date}");
            $this->line("  Time: {$reservation->reservation_time}");
            $this->line("  Status: {$reservation->status}");
            $this->line("  Customer: " . ($reservation->customer ? $reservation->customer->name : 'NULL'));
            $this->line("  Restaurant: " . ($reservation->restaurant ? $reservation->restaurant->name : 'NULL'));
            $this->line("  Table: " . ($reservation->table ? $reservation->table->table_name : 'NULL'));
            $this->line("  Guests: {$reservation->number_of_guests}");
            $this->line("---");
        }
        
        // 7. Check if there are any customer users
        $customerUsers = User::where('role', 'customer')->count();
        $this->info("Customer users in system: {$customerUsers}");
        
        if ($customerUsers === 0) {
            $this->warn('⚠️  NO CUSTOMER USERS FOUND! You need customer accounts to make reservations.');
        }
        
        return 0;
    }
}