<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Common password for all test users
        $password = Hash::make('password');

        // Admin user - Full system access
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@abodeology.com',
            'phone' => '+44 20 1234 5678',
            'role' => 'admin',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // Agent user - Internal staff
        User::create([
            'name' => 'Agent User',
            'email' => 'agent@abodeology.com',
            'phone' => '+44 20 1234 5679',
            'role' => 'agent',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // Buyer user - Searches & buys
        User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@abodeology.com',
            'phone' => '+44 20 1234 5680',
            'role' => 'buyer',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // Seller user - Lists properties
        User::create([
            'name' => 'Seller User',
            'email' => 'seller@abodeology.com',
            'phone' => '+44 20 1234 5681',
            'role' => 'seller',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // Both (Buyer & Seller) user
        User::create([
            'name' => 'Buyer Seller User',
            'email' => 'both@abodeology.com',
            'phone' => '+44 20 1234 5682',
            'role' => 'both',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // PVA (Property Viewing Assistant) user
        User::create([
            'name' => 'PVA User',
            'email' => 'pva@abodeology.com',
            'phone' => '+44 20 1234 5683',
            'role' => 'pva',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Created users for all roles:');
        $this->command->info('   Admin:  admin@abodeology.com / password');
        $this->command->info('   Agent:  agent@abodeology.com / password');
        $this->command->info('   Buyer:  buyer@abodeology.com / password');
        $this->command->info('   Seller: seller@abodeology.com / password');
        $this->command->info('   Both:   both@abodeology.com / password');
        $this->command->info('   PVA:    pva@abodeology.com / password');
    }
}
