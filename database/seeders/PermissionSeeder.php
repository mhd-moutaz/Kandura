<?php

namespace Database\Seeders;

use App\Enum\UserRoleEnum;

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
        $permissions = [
            // Profile --------------
            'create profile',
            'update profile',
            'delete profile',
            'view profile',
            // Address --------------
            'create address',
            'update address',
            'delete address',
            'view address',
            // Measurement --------------
            'create measurement',
            'update measurement',
            'delete measurement',
            'view measurement',
            // Design --------------
            'create design',
            'update design',
            'delete design',
            'view design',
            // Order --------------
            'create order',
            'view order',
            // Wallet --------------
            'view wallet',
            'view transactions',
            // Review --------------
            'create review',
            'view review',
            // Notifications --------------
            'view notifications',
            // Design (admin) --------------
            'view all designs',
            'edit all designs',
            'delete all designs',
            // Orders (admin) --------------
            'view all orders',
            'change order status',
            // Wallet (admin) --------------
            'manage wallet',
            // Review (admin) --------------
            'view all reviews',
            'approve review',
            'reject review',
            'delete review',
            // Notifications --------------
            'send notifications',
            // Coupons --------------
            'create coupon',
            'update coupon',
            'delete coupon',
            'view coupon',
            // Design options --------------
            'manage design options',
            // Users --------------
            'view all users',
            'disable user',
            'delete user',
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
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $userRole = Role::create(
            ['name' => UserRoleEnum::USER, 'guard_name' => 'web']
        );
        $userRole->givePermissionTo(
            [
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
                'view order',
                // Wallet
                'view wallet',
                'view transactions',
                // Reviews
                'create review',
                'view review',
                // Notifications
                'view notifications',
            ]
        );
        $AdminRole = Role::create(
            ['name' => UserRoleEnum::ADMIN, 'guard_name' => 'web']
        );
        $AdminRole->givePermissionTo(
            [
                // User Management
                'view all users','disable user','delete user',
                // Order Management
                'view all orders','change order status',
                // Design Management (all designs)
                'view all designs','edit all designs','delete all designs',
                // Coupon Management
                'create coupon','update coupon','delete coupon','view coupon',
                // Design Options
                'manage design options',
                // Review Management
                'view all reviews','approve review','reject review','delete review',
                // Notifications
                'send notifications',
                // Wallet Management
                'manage wallet',
            ]
        );
        $SuperAdminRole = Role::create(
            ['name' => UserRoleEnum::SUPER_ADMIN, 'guard_name' => 'web']
        );
        $SuperAdminRole->givePermissionTo(
            Permission::all()
        );
    }
}
