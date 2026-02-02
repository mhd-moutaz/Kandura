<?php

namespace App\Constants;

/**
 * Permission Constants
 *
 * Centralized permission string constants to avoid typos and maintain consistency.
 * These constants reference permissions defined in config/role_permissions.php
 */
class PermissionConstants
{
    // ============ API Guard (User) Permissions ============

    // Profile
    const VIEW_PROFILE = 'view profile';
    const UPDATE_PROFILE = 'update profile';
    const DELETE_PROFILE = 'delete profile';

    // Address
    const VIEW_ADDRESS = 'view address';
    const CREATE_ADDRESS = 'create address';
    const UPDATE_ADDRESS = 'update address';
    const DELETE_ADDRESS = 'delete address';

    // Design (Own)
    const VIEW_DESIGN = 'view designs';
    const CREATE_DESIGN = 'create design';
    const UPDATE_DESIGN = 'update design';
    const DELETE_DESIGN = 'delete design';

    // Orders (Own)
    const VIEW_ORDER = 'view order';
    const CREATE_ORDER = 'create order';
    const UPDATE_ORDER = 'update order';
    const DELETE_ORDER = 'delete order';

    // Coupon
    const APPLY_COUPON = 'apply coupon';
    const REMOVE_COUPON = 'remove coupon';
    const VALIDATE_COUPON = 'validate coupon';

    // Invoice
    const VIEW_INVOICE = 'view invoice';

    // Wallet
    const VIEW_WALLET = 'view wallet';
    const VIEW_TRANSACTIONS = 'view transactions';

    // Reviews (Own)
    const VIEW_REVIEW = 'view review';
    const CREATE_REVIEW = 'create review';
    const DELETE_REVIEW = 'delete review';

    // Notifications
    const VIEW_NOTIFICATIONS = 'view notifications';

    // ============ Web Guard (Admin/Super Admin) Permissions ============

    // User Management
    const VIEW_ALL_USERS = 'view all user';
    const UPDATE_USER = 'update user';
    const DELETE_USER = 'delete user';

    // Address Management
    const VIEW_ALL_ADDRESS = 'view all address';

    // Design Options
    const VIEW_DESIGN_OPTIONS = 'view design options';
    const CREATE_DESIGN_OPTIONS = 'create design options';
    const UPDATE_DESIGN_OPTIONS = 'update design options';
    const DELETE_DESIGN_OPTIONS = 'delete design options';

    // Order Management
    const VIEW_ALL_ORDERS = 'view all order';
    const UPDATE_ORDER_STATUS = 'update status order';

    // Invoice Management
    const VIEW_INVOICES = 'view invoice';
    const REGENERATE_INVOICE = 'regenerate invoice';

    // Design Management (All)
    const VIEW_ALL_DESIGNS = 'view all designs';
    const UPDATE_ALL_DESIGNS = 'update all designs';
    const DELETE_ALL_DESIGNS = 'delete all designs';

    // Wallet Management
    const VIEW_USER_WALLET = 'view user wallet';
    const DEPOSIT_TO_WALLET = 'deposit to wallet';
    const WITHDRAW_FROM_WALLET = 'withdraw from wallet';

    // Coupon Management
    const VIEW_ALL_COUPON = 'view all coupon';
    const VIEW_COUPON = 'view coupon';
    const CREATE_COUPON = 'create coupon';
    const UPDATE_COUPON = 'update coupon';
    const DELETE_COUPON = 'delete coupon';

    // Review Management
    const VIEW_ALL_REVIEWS = 'view all review';
    const APPROVE_REVIEW = 'approve review';
    const REJECT_REVIEW = 'reject review';
    const DELETE_REVIEW_ADMIN = 'delete review'; // Same as user but different context

    // Notifications
    const MANAGE_NOTIFICATIONS = 'manage notifications';

    // Admin Management (Super Admin Only)
    const VIEW_ALL_ADMINS = 'view all admin';
    const VIEW_ADMIN = 'view admin';
    const CREATE_ADMIN = 'create admin';
    const UPDATE_ADMIN = 'update admin';
    const DELETE_ADMIN = 'delete admin';

    // Role Management (Super Admin Only)
    const VIEW_ALL_ROLES = 'view all role';
    const VIEW_ROLE = 'view role';
    const CREATE_ROLE = 'create role';
    const UPDATE_ROLE = 'update role';
    const DELETE_ROLE = 'delete role';
}
