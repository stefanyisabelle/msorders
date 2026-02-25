<?php

namespace Database\Seeders;

use App\Models\Order;
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
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
        ]);

        // Create sample orders for the regular user
        Order::create([
            'customer_name' => 'João Silva',
            'destination' => 'São Paulo',
            'departure_date' => now()->addDays(10),
            'return_date' => now()->addDays(15),
            'status' => Order::STATUS_PENDING,
            'user_id' => $user->id,
        ]);

        Order::create([
            'customer_name' => 'Maria Santos',
            'destination' => 'Rio de Janeiro',
            'departure_date' => now()->addDays(20),
            'return_date' => now()->addDays(25),
            'status' => Order::STATUS_CONFIRMED,
            'user_id' => $user->id,
        ]);

        Order::create([
            'customer_name' => 'Pedro Oliveira',
            'destination' => 'Belo Horizonte',
            'departure_date' => now()->addDays(5),
            'return_date' => now()->addDays(8),
            'status' => Order::STATUS_PENDING,
            'user_id' => $admin->id,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('User: user@example.com / password');
    }
}
