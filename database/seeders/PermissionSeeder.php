<?php

namespace Database\Seeders;

use App\Enum\UserRoleEnum;
use App\Http\Enum\RoleUserEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الـ Permissions لكل guard من config
        foreach (config('role_permissions.guards') as $guard => $names) {
            $this->seed_for_guard($guard, $names);
        }

        // ====== User Role للـ API ======
        $userRoleApi = Role::create([
            'name' => UserRoleEnum::USER,
            'guard_name' => 'api'
        ]);

        $userRoleApi->givePermissionTo(config('role_permissions.user'));

        // ====== Admin Role للـ Web ======
        $AdminRole = Role::create([
            'name' => UserRoleEnum::ADMIN,
            'guard_name' => 'web'
        ]);

        $AdminRole->givePermissionTo(config('role_permissions.admin'));

        // ====== Stock Manager Role للـ Web ======
        $StockManagerRole = Role::create([
            'name' => 'stock_manager',
            'guard_name' => 'web'
        ]);

        $StockManagerRole->givePermissionTo(config('role_permissions.stock_manager'));

        // ====== Super Admin Role للـ Web ======
        $SuperAdminRole = Role::create([
            'name' => UserRoleEnum::SUPER_ADMIN,
            'guard_name' => 'web'
        ]);

        
        $SuperAdminRole->givePermissionTo(
            config('role_permissions.super_admin')
        );
    }

    public function seed_for_guard(string $guard, array $names)
    {
        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }
    }
}
