<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\SeatingLayout;
use App\Models\SeatingZone;
use App\Models\Table;

class SeatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first restaurant (assuming there's at least one)
        $restaurant = Restaurant::first();
        
        if (!$restaurant) {
            $this->command->warn('No restaurant found. Please create a restaurant first.');
            return;
        }

        // Check if seating layout already exists for this restaurant
        $existingLayout = SeatingLayout::where('restaurant_id', $restaurant->id)->first();
        if ($existingLayout) {
            $this->command->info('Seating layout already exists for ' . $restaurant->name);
            return;
        }

        // Create seating layout
        $layout = SeatingLayout::create([
            'restaurant_id' => $restaurant->id,
            'layout_name' => 'Main Dining Area',
            'description' => 'Main dining area with various seating zones',
            'width' => 800,
            'height' => 600,
            'layout_data' => [
                'background' => '#f8f9fa',
                'zones' => [
                    ['id' => 1, 'name' => 'Window Side', 'color' => '#e3f2fd'],
                    ['id' => 2, 'name' => 'Center Area', 'color' => '#f3e5f5'],
                    ['id' => 3, 'name' => 'Bar Area', 'color' => '#fff3e0']
                ]
            ],
            'is_active' => true,
            'is_default' => true
        ]);

        // Create seating zones
        $zones = [
            [
                'zone_name' => 'Window Side',
                'description' => 'Tables near the windows with natural lighting',
                'zone_coordinates' => ['x' => 0, 'y' => 0, 'width' => 400, 'height' => 300],
                'zone_color' => '#e3f2fd',
                'max_capacity' => 20,
                'amenities' => ['natural_light', 'window_view']
            ],
            [
                'zone_name' => 'Center Area',
                'description' => 'Main dining area in the center of the restaurant',
                'zone_coordinates' => ['x' => 400, 'y' => 0, 'width' => 400, 'height' => 300],
                'zone_color' => '#f3e5f5',
                'max_capacity' => 30,
                'amenities' => ['air_conditioning', 'music']
            ],
            [
                'zone_name' => 'Bar Area',
                'description' => 'High-top tables near the bar',
                'zone_coordinates' => ['x' => 0, 'y' => 300, 'width' => 800, 'height' => 300],
                'zone_color' => '#fff3e0',
                'max_capacity' => 15,
                'amenities' => ['bar_access', 'tv_screens']
            ]
        ];

        $createdZones = [];
        foreach ($zones as $zoneData) {
            $zone = SeatingZone::create([
                'restaurant_id' => $restaurant->id,
                'seating_layout_id' => $layout->id,
                ...$zoneData,
                'is_active' => true
            ]);
            $createdZones[] = $zone;
        }

        // Create tables
        $tables = [
            // Window Side Tables
            ['table_name' => 'W1', 'capacity' => 2, 'position' => ['x' => 50, 'y' => 50], 'seating_zone_id' => $createdZones[0]->id],
            ['table_name' => 'W2', 'capacity' => 4, 'position' => ['x' => 150, 'y' => 50], 'seating_zone_id' => $createdZones[0]->id],
            ['table_name' => 'W3', 'capacity' => 2, 'position' => ['x' => 250, 'y' => 50], 'seating_zone_id' => $createdZones[0]->id],
            ['table_name' => 'W4', 'capacity' => 6, 'position' => ['x' => 50, 'y' => 150], 'seating_zone_id' => $createdZones[0]->id],
            ['table_name' => 'W5', 'capacity' => 4, 'position' => ['x' => 200, 'y' => 150], 'seating_zone_id' => $createdZones[0]->id],
            ['table_name' => 'W6', 'capacity' => 2, 'position' => ['x' => 300, 'y' => 150], 'seating_zone_id' => $createdZones[0]->id],
            
            // Center Area Tables
            ['table_name' => 'C1', 'capacity' => 4, 'position' => ['x' => 450, 'y' => 50], 'seating_zone_id' => $createdZones[1]->id],
            ['table_name' => 'C2', 'capacity' => 6, 'position' => ['x' => 550, 'y' => 50], 'seating_zone_id' => $createdZones[1]->id],
            ['table_name' => 'C3', 'capacity' => 2, 'position' => ['x' => 650, 'y' => 50], 'seating_zone_id' => $createdZones[1]->id],
            ['table_name' => 'C4', 'capacity' => 8, 'position' => ['x' => 450, 'y' => 150], 'seating_zone_id' => $createdZones[1]->id],
            ['table_name' => 'C5', 'capacity' => 4, 'position' => ['x' => 600, 'y' => 150], 'seating_zone_id' => $createdZones[1]->id],
            ['table_name' => 'C6', 'capacity' => 2, 'position' => ['x' => 700, 'y' => 150], 'seating_zone_id' => $createdZones[1]->id],
            
            // Bar Area Tables
            ['table_name' => 'B1', 'capacity' => 2, 'position' => ['x' => 50, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B2', 'capacity' => 4, 'position' => ['x' => 150, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B3', 'capacity' => 2, 'position' => ['x' => 250, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B4', 'capacity' => 6, 'position' => ['x' => 350, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B5', 'capacity' => 4, 'position' => ['x' => 500, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B6', 'capacity' => 2, 'position' => ['x' => 600, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
            ['table_name' => 'B7', 'capacity' => 4, 'position' => ['x' => 700, 'y' => 350], 'seating_zone_id' => $createdZones[2]->id],
        ];

        foreach ($tables as $tableData) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'seating_layout_id' => $layout->id,
                'table_name' => $tableData['table_name'],
                'capacity' => $tableData['capacity'],
                'status' => 'available',
                'location' => 'Main Dining',
                'position' => $tableData['position'],
                'table_coordinates' => [
                    'x' => $tableData['position']['x'],
                    'y' => $tableData['position']['y'],
                    'width' => 80,
                    'height' => 60
                ],
                'table_rotation' => 0,
                'table_shape' => 'rectangle',
                'table_dimensions' => ['width' => 80, 'height' => 60],
                'seating_zone_id' => $tableData['seating_zone_id'],
                'is_active' => true
            ]);
        }

        $this->command->info('Seating layout, zones, and tables created successfully!');
    }
}
