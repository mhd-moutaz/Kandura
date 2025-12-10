<?php

namespace Database\Seeders;

use App\Enum\UserRoleEnum;
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

        // 3. Regular User (API)
        $user = User::create([
            'name' => 'moutaz',
            'email' => 'moutaz@gmail.com',
            'password' => Hash::make('123456'),
            'phone' => '1234567892',
            'role' => UserRoleEnum::USER,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user'); // رح يستخدم api guard تلقائياً من getDefaultGuardName()

        // 4. Additional Regular User (API)
        $user2 = User::create([
            'name' => 'moutaz1',
            'email' => 'moutaz1@gmail.com',
            'password' => Hash::make('123456'),
            'phone' => '1234567893',
            'role' => UserRoleEnum::USER,
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user'); // رح يستخدم api guard تلقائياً من getDefaultGuardName()
    }
}
