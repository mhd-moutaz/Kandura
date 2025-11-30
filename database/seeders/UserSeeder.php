<?php

namespace Database\Seeders;

use App\Enum\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'role' => UserRoleEnum::SUPER_ADMIN,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // 2. Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567891',
            'role' => UserRoleEnum::ADMIN,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // 3. Regular User
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567892',
            'role' => UserRoleEnum::USER,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // 4. Additional Regular User
        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567893',
            'role' => UserRoleEnum::USER,
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');
    }
}
