<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin and staff users first
        $this->call(AdminStaffSeeder::class);

        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        // Seed default restaurants with images so cards show pictures immediately
        $hours = [
            'mon' => ['open' => '10:00', 'close' => '22:00'],
            'tue' => ['open' => '10:00', 'close' => '22:00'],
            'wed' => ['open' => '10:00', 'close' => '22:00'],
            'thu' => ['open' => '10:00', 'close' => '22:00'],
            'fri' => ['open' => '10:00', 'close' => '23:00'],
            'sat' => ['open' => '10:00', 'close' => '23:00'],
            'sun' => ['open' => '10:00', 'close' => '22:00'],
        ];

        // Get or create a staff user for Din Tai Fung
        $dtfUser = User::firstOrCreate(
            ['email' => 'staff@dintaifung.local'],
            [
                'name' => 'Din Tai Fung Staff',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true
            ]
        );

        Restaurant::query()->firstOrCreate(
            ['name' => 'Din Tai Fung'],
            [
                'user_id' => $dtfUser->id,
                'description' => 'World-famous Taiwanese dim sum and noodles.',
                'address' => 'BGC, Taguig, PH',
                'phone' => '+63 2 2345 6789',
                'email' => 'hello@dintaifung.local',
                'website' => null,
                'operating_hours' => $hours,
                'max_capacity' => 200,
                'amenities' => ['WiFi'],
                'logo' => 'restaurants/DTF.png',
                'images' => [
                    'restaurants/DTF.png',
                ],
                'is_active' => true,
            ]
        );

        // Get or create a staff user for Vikings
        $vikingsUser = User::firstOrCreate(
            ['email' => 'staff@vikings.local'],
            [
                'name' => 'Vikings Staff',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true
            ]
        );

        Restaurant::query()->firstOrCreate(
            ['name' => 'Vikings'],
            [
                'user_id' => $vikingsUser->id,
                'description' => 'Buffet restaurant with wide international selection.',
                'address' => 'Mall of Asia, Pasay, PH',
                'phone' => '+63 2 3456 7890',
                'email' => 'reservations@vikings.local',
                'website' => null,
                'operating_hours' => $hours,
                'max_capacity' => 300,
                'amenities' => ['Parking', 'WiFi', 'Private Rooms'],
                'logo' => 'restaurants/vikings.jpg',
                'images' => [
                    'restaurants/vikings.jpg',
                ],
                'is_active' => true,
            ]
        );

        // Get or create a staff user for Wolfgang
        $wolfgangUser = User::firstOrCreate(
            ['email' => 'staff@wolfgang.local'],
            [
                'name' => 'Wolfgang Staff',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true
            ]
        );

        Restaurant::query()->firstOrCreate(
            ['name' => 'Wolfgang'],
            [
                'user_id' => $wolfgangUser->id,
                'description' => 'Premium steakhouse dining experience.',
                'address' => 'Makati City, PH',
                'phone' => '+63 2 1234 5678',
                'email' => 'contact@wolfgang.local',
                'website' => null,
                'operating_hours' => $hours,
                'max_capacity' => 150,
                'amenities' => ['Parking', 'WiFi'],
                'logo' => 'restaurants/Wolfgang.png',
                'images' => [
                    'restaurants/Wolfgang.png',
                ],
                'is_active' => true,
            ]
        );

        // Seed tables for each restaurant
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            // Create 8-12 tables per restaurant with different capacities
            $tableCount = $restaurant->name === 'Vikings' ? 12 : ($restaurant->name === 'Din Tai Fung' ? 10 : 8);
            
            for ($i = 1; $i <= $tableCount; $i++) {
                $capacity = match(true) {
                    $i <= 3 => 2, // Small tables for couples
                    $i <= 6 => 4, // Medium tables for small groups
                    $i <= 8 => 6, // Large tables for families
                    default => 8   // Extra large tables
                };
                
                \App\Models\Table::query()->firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'table_name' => 'Table ' . $i,
                    ],
                    [
                        'capacity' => $capacity,
                        'status' => 'available',
                        'location' => match(true) {
                            $i <= 2 => 'Window',
                            $i <= 4 => 'Center',
                            $i <= 6 => 'Corner',
                            default => 'Main Area'
                        },
                        'is_active' => true,
                    ]
                );
            }
        }
        
        // Create seating layouts and zones after restaurants are created
        $this->call(SeatingSeeder::class);
    }
}
