<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all permission definitions for each role in the system.
    | Permissions are organized by guard (api/web) and role.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | All Permissions by Guard
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'api' => [
            // Profile
            'view profile',
            'update profile',
            'delete profile',

            // Address
            'view address',
            'create address',
            'update address',
            'delete address',

            // Design (own only)
            'view designs',
            'create designs',
            'update designs',
            'delete designs',

            // Orders
            'view order',
            'create order',
            'update order',
            'delete order',

            // Coupon
            'apply coupon',
            'remove coupon',
            'validate coupon',

            // Invoice
            'view invoice',

            // Wallet
            'view wallet',
            'view transactions',

            // Reviews
            'create review',
            'view review',
            'delete review',

            // Notifications
            'view notifications',
        ],

        'web' => [
            // User Management
            'view all user',
            'update user',
            'delete user',

            // Address Management
            'view all address',

            // Design Options
            'view design options',
            'create design options',
            'update design options',
            'delete design options',

            // Order Management
            'view all order',
            'view order',
            'update status order',

            // Invoice Management
            'view invoice',
            'regenerate invoice',

            // Design Management (all designs)
            'view all designs',
            'view designs',
            'update all designs',
            'delete all designs',

            // Wallet Management
            'view user wallet',
            'deposit to wallet',
            'withdraw from wallet',

            // Coupon Management
            'view all coupon',
            'create coupon',
            'update coupon',
            'delete coupon',
            'view coupon',

            // Review Management
            'view all review',
            'approve review',
            'reject review',
            'delete review',

            // Notifications
            'manage notifications',

            // Profile (for admin panel)
            'view profile',
            'update profile',

            // Admin Management (Super Admin Only)
            'view all admin',
            'view admin',
            'create admin',
            'update admin',
            'delete admin',

            // Role Management (Super Admin Only)
            'view all role',
            'view role',
            'create role',
            'update role',
            'delete role',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role-Specific Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * User role permissions (API guard)
     */
    'user' => [
        // Profile
        'view profile',
        'update profile',
        'delete profile',

        // Address
        'view address',
        'create address',
        'update address',
        'delete address',

        // Design (own only)
        'view designs',
        'create designs',
        'update designs',
        'delete designs',

        // Orders
        'view order',
        'create order',
        'update order',
        'delete order',

        // Coupon
        'apply coupon',
        'remove coupon',
        'validate coupon',

        // Invoice
        'view invoice',

        // Wallet
        'view wallet',
        'view transactions',

        // Reviews
        'view review',
        'create review',
        'delete review',

        // Notifications
        'view notifications',
    ],

    /**
     * Admin role permissions (Web guard)
     */
    'admin' => [
        // User Management
        'view all user',
        'update user',
        'delete user',

        // Address Management
        'view all address',

        // Design Options
        'view design options',
        'create design options',
        'update design options',
        'delete design options',

        // Order Management
        'view all order',
        'view order',
        'update status order',

        // Invoice Management
        'view invoice',
        'regenerate invoice',

        // Design Management (all designs)
        'view all designs',
        'view designs',
        'update all designs',
        'delete all designs',

        // Wallet Management
        'view user wallet',
        'deposit to wallet',
        'withdraw from wallet',

        // Coupon Management
        'view all coupon',
        'create coupon',
        'update coupon',
        'delete coupon',
        'view coupon',

        // Review Management
        'view all review',
        'approve review',
        'reject review',
        'delete review',

        // Notifications
        'manage notifications',
    ],

    /**
     * Stock Manager role permissions (Web guard)
     */
    'stock_manager' => [
        // Design Management (all designs)
        'view all designs',
        'view designs',
        'update all designs',
        'delete all designs',

        // Order Management
        'view all order',
        'view order',
        'update status order',

        // Invoice Management
        'view invoice',
        'regenerate invoice',
    ],

    /**
     * Super Admin role permissions (Web guard)
     * Note: Super Admin gets ALL web guard permissions
     */
    'super_admin' => [
        // User Management
        'view all user',
        'update user',
        'delete user',

        // Address Management
        'view all address',

        // Design Options
        'view design options',
        'create design options',
        'update design options',
        'delete design options',

        // Order Management
        'view all order',
        'view order',
        'update status order',

        // Invoice Management
        'view invoice',
        'regenerate invoice',

        // Design Management (all designs)
        'view all designs',
        'view designs',
        'update all designs',
        'delete all designs',

        // Wallet Management
        'view user wallet',
        'deposit to wallet',
        'withdraw from wallet',

        // Coupon Management
        'view all coupon',
        'create coupon',
        'update coupon',
        'delete coupon',
        'view coupon',

        // Review Management
        'view all review',
        'approve review',
        'reject review',
        'delete review',

        // Notifications
        'manage notifications',

        // Profile (for admin panel)
        'view profile',
        'update profile',

        // Admin Management (Super Admin Only)
        'view all admin',
        'view admin',
        'create admin',
        'update admin',
        'delete admin',

        // Role Management (Super Admin Only)
        'view all role',
        'view role',
        'create role',
        'update role',
        'delete role',
    ],
];
