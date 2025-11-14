<?php

namespace App\Console\Commands;

use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Console\Command;

class DebugTableReservations extends Command
{
    protected $signature = 'debug:table-reservations';
    protected $description = 'Debug table-reservation relationships';

    public function handle()
    {
        $this->info('=== TABLE-RESERVATION DEBUG ===');
        
        // Get all tables with their reservations
        $tables = Table::with(['reservations.customer'])->get();
        
        foreach ($tables as $table) {
            $this->line("Table: {$table->table_name} (ID: {$table->id})");
            $this->line("  Status: {$table->status}");
            $this->line("  Reservations count: " . $table->reservations->count());
            
            if ($table->reservations->count() > 0) {
                foreach ($table->reservations as $reservation) {
                    $this->line("    Reservation ID: {$reservation->id}");
                    $this->line("    Date: {$reservation->reservation_date}");
                    $this->line("    Time: " . ($reservation->reservation_time ? $reservation->reservation_time : 'NULL'));
                    $this->line("    Customer: " . ($reservation->customer ? $reservation->customer->name : 'NULL'));
                    $this->line("    Status: {$reservation->status}");
                    $this->line("    ---");
                }
            }
            $this->line("");
        }
        
        // Check for today's reservations
        $this->info('=== TODAY\'S RESERVATIONS ===');
        $todayReservations = Reservation::where('reservation_date', now()->toDateString())
            ->with(['customer', 'table'])
            ->get();
            
        $this->info("Today's reservations count: " . $todayReservations->count());
        
        foreach ($todayReservations as $reservation) {
            $this->line("Reservation ID: {$reservation->id}");
            $this->line("  Customer: " . ($reservation->customer ? $reservation->customer->name : 'NULL'));
            $this->line("  Time: " . ($reservation->reservation_time ? $reservation->reservation_time : 'NULL'));
            $this->line("  Table: " . ($reservation->table ? $reservation->table->table_name : 'NULL'));
            $this->line("  Status: {$reservation->status}");
        }
        
        return 0;
    }
}