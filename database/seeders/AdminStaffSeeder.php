<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@tabledin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create Staff User
        User::firstOrCreate(
            ['email' => 'staff@tabledin.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create another Staff User
        User::firstOrCreate(
            ['email' => 'manager@tabledin.com'],
            [
                'name' => 'Restaurant Manager',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
