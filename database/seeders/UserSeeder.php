<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'reception@hotel.com'],
            [
                'name' => 'Reception User',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
            ]
        );

        User::updateOrCreate(
            ['email' => 'housekeeping@hotel.com'],
            [
                'name' => 'Housekeeping User',
                'password' => Hash::make('password123'),
                'role' => 'housekeeping',
            ]
        );
    }
}
