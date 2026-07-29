<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Account Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Account User Biasa
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Biasa',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
