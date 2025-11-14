<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class DebugReservationTable extends Command
{
    protected $signature = 'debug:reservation-table';
    protected $description = 'Debug reservation table relationships';

    public function handle()
    {
        $this->info('=== RESERVATION TABLE DEBUG ===');
        
        // Get all reservations with their table relationships
        $reservations = Reservation::with(['customer', 'table'])->get();
        
        foreach ($reservations as $reservation) {
            $this->line("Reservation ID: {$reservation->id}");
            $this->line("  Customer: " . ($reservation->customer ? $reservation->customer->name : 'NULL'));
            $this->line("  Table ID: " . ($reservation->table_id ?: 'NULL'));
            $this->line("  Table Object: " . ($reservation->table ? $reservation->table->table_name : 'NULL'));
            $this->line("  Date: {$reservation->reservation_date}");
            $this->line("  Time: {$reservation->reservation_time}");
            $this->line("  Status: {$reservation->status}");
            $this->line("---");
        }
        
        // Test the specific case that might be causing the error
        $this->info('=== TESTING NULL TABLE ACCESS ===');
        $reservationsWithoutTable = Reservation::whereNull('table_id')->with(['customer', 'table'])->get();
        
        foreach ($reservationsWithoutTable as $reservation) {
            $this->line("Reservation ID: {$reservation->id} (no table_id)");
            $this->line("  Table relationship: " . ($reservation->table ? $reservation->table->table_name : 'NULL (correct)'));
            
            // Test the exact code that's failing
            try {
                if ($reservation->table) {
                    $tableName = $reservation->table->table_name;
                    $this->line("  ✓ Table name access: {$tableName}");
                } else {
                    $this->line("  ✓ Table is null, should show 'Unassigned'");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error accessing table: " . $e->getMessage());
            }
        }
        
        return 0;
    }
}