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
            'role' => UserRoleEnum::SUPER_ADMIN, // Keep for backward compatibility
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');
    }
}
