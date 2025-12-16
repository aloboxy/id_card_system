<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Import DB facade

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if any user exists
        $user = User::first();

        if ($user) {
            // Promote the first user to admin
            $user->update(['role' => 'admin']);
            $this->command->info("User '{$user->email}' promoted to admin.");
        } else {
            // Create a default admin user
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
            $this->command->info("Default admin user created (admin@example.com / password).");
        }
    }
}
