<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('Admin@123456'),
                'role' => Role::ADMIN,
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@example.com');
            $this->command->info('Password: Admin@123456');
        } else {
            $this->command->warn('Admin user already exists with email: admin@example.com');
        }
    }
}
