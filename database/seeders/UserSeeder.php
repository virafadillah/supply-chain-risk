<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hasIsAdmin = Schema::hasColumn('users', 'is_admin');

        // Account Admin
        $adminData = [
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ];
        if ($hasIsAdmin) {
            $adminData['is_admin'] = true;
        }

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            $adminData
        );

        // Account User Biasa
        $userData = [
            'name' => 'User Biasa',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ];
        if ($hasIsAdmin) {
            $userData['is_admin'] = false;
        }

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            $userData
        );
    }
}
