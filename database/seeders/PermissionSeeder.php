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
        // إنشاء الـ Permissions لكل guard
        foreach ($this->permissions() as $guard => $names) {
            $this->seed_for_guard($guard, $names);
        }

        // ====== User Role للـ API ======
        $userRoleApi = Role::create([
            'name' => UserRoleEnum::USER,
            'guard_name' => 'api'
        ]);
        $userRoleApi->givePermissionTo([
            // Profile
            'create profile',
            'update profile',
            'delete profile',
            'view profile',
            // Address
            'create address',
            'update address',
            'delete address',
            'view address',
            // Measurements
            'create measurement',
            'update measurement',
            'delete measurement',
            'view measurement',
            // Design (own only)
            'create design',
            'update design',
            'delete design',
            'view design',
            // Orders
            'create order',
            'update order',
            'delete order',
            'view order',
            // Wallet
            'view wallet',
            'view transactions',
            // Reviews
            'create review',
            'view review',
            // Notifications
            'view notifications',
        ]);

        $AdminRole = Role::create([
            'name' => UserRoleEnum::ADMIN,
            'guard_name' => 'web'
        ]);

        $SuperAdminRole = Role::create([
            'name' => UserRoleEnum::SUPER_ADMIN,
            'guard_name' => 'web'
        ]);
        $SuperAdminRole->givePermissionTo(
            Permission::where('guard_name', 'web')->get()
        );
    }

    public function permissions()
    {
        return [
            'api' => [
                //--------------User
                // Profile
                'create profile',
                'update profile',
                'delete profile',
                'view profile',
                // Address
                'create address',
                'update address',
                'delete address',
                'view address',
                // Measurements
                'create measurement',
                'update measurement',
                'delete measurement',
                'view measurement',
                // Design (own only)
                'create design',
                'update design',
                'delete design',
                'view design',
                // Orders
                'create order',
                'update order',
                'delete order',
                'view order',
                // Wallet
                'view wallet',
                'view transactions',
                // Reviews
                'create review',
                'view review',
                // Notifications
                'view notifications',
            ],
            'web' => [
                // User Management
                'view all users',
                'disable user',
                'delete user',
                // Order Management
                'view all orders',
                'change order status',
                // Design Management (all designs)
                'view all designs',
                'edit all designs',
                'delete all designs',
                // Address Management
                'view all address',
                // Coupon Management
                'create coupon',
                'update coupon',
                'delete coupon',
                'view coupon',
                // Design Options
                'manage design options',
                // Review Management
                'view all reviews',
                'approve review',
                'reject review',
                'delete review',
                // Notifications
                'send notifications',
                // Wallet Management
                'manage wallet',
                // Admin management (super admin) --------------
                'create admin',
                'update admin',
                'delete admin',
                'view admin',
                'manage system settings',
                'view reports',
                'view statistics',
                'manage roles',
                'manage permissions',
            ]
        ];
    }

    public function seed_for_guard(string $guard, array $names)
    {
        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }
    }
}
