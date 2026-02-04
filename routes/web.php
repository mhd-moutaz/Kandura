<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admins\AuthController;
use App\Http\Controllers\Admins\UserController;
use App\Http\Controllers\Admins\OrderController;
use App\Http\Controllers\Users\StripeController;
use App\Http\Controllers\Admins\DesignController;
use App\Http\Controllers\Admins\WalletController;
use App\Http\Controllers\Admins\AddressController;
use App\Http\Controllers\Admins\DashboardController;
use App\Http\Controllers\Admins\DesignOptionsController;
use App\Http\Controllers\Admins\CouponController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;
use App\Http\Controllers\SuperAdmin\RoleManagementController;
use App\Http\Controllers\Admins\ReviewController;
use App\Http\Controllers\Admins\InvoiceController;
use App\Http\Controllers\Admins\NotificationController;

Route::get("", function () {
    return redirect()->route("login");
});

Route::get("login", [AuthController::class, "loginView"])->name("login");
Route::post("login", [AuthController::class, "login"])->name("login_action");
Route::get('stripe/order/success', [StripeController::class, 'orderSuccess'])->name('stripe.order.success');
Route::get('stripe/order/cancel', [StripeController::class, 'orderCancel'])->name('stripe.order.cancel');

Route::middleware(['auth'])->group(function () {

    Route::get("dashboard", [DashboardController::class, "index"])->name("home");
    Route::post("logout", [AuthController::class, "logout"])->name("logout");

    // Notification routes for admins
    // Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
    //     Route::post('/update-fcm-token', [NotificationController::class, 'updateFcmToken'])->name('update-fcm-token');
    //     Route::post('/test-notification', [NotificationController::class, 'sendTestNotification'])->name('test')->middleware('permission:send notifications');
    //     Route::post('/send-to-users', [NotificationController::class, 'sendToMultipleUsers'])->name('send-to-users')->middleware('permission:send notifications');
    //     Route::post('/send-to-admins', [NotificationController::class, 'sendToAllAdmins'])->name('send-to-admins')->middleware('permission:send notifications');
    //     Route::get('/', [NotificationController::class, 'index'])->name('index');
    //     Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    //     Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    //     Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    // });

    // Routes for users management
    Route::prefix('users')->group(function () {
        Route::get("", [UserController::class, "index"])->name("users.index")->middleware("permission:view all user");
        Route::get("{user}/edit", [UserController::class, "edit"])->name("users.edit")->middleware("permission:update user");
        Route::put("{user}", [UserController::class, "update"])->name("users.update")->middleware("permission:update user");
        Route::delete("{user}", [UserController::class, "destroy"])->name("users.destroy")->middleware("permission:delete user");
    });

    // Routes for addresses management
    Route::prefix('addresses')->group(function () {
        Route::get("", [AddressController::class, "index"])->name("addresses.index")->middleware('permission:view all address');

    });

    // Routes for design options management
    Route::prefix('designOptions')->group(function () {
        Route::get("", [DesignOptionsController::class, "index"])->name("designOptions.index")->middleware('permission:view design options');
        Route::get('/create', [DesignOptionsController::class, 'create'])->name('designOptions.create')->middleware('permission:create design options');
        Route::post('/store', [DesignOptionsController::class, 'store'])->name('designOptions.store')->middleware('permission:create design options');
        Route::get("{designOption}/edit", [DesignOptionsController::class, "edit"])->name("designOptions.edit")->middleware('permission:update design options');
        Route::put("{designOption}", [DesignOptionsController::class, "update"])->name("designOptions.update")->middleware('permission:update design options');
        Route::delete("{designOption}", [DesignOptionsController::class, "destroy"])->name("designOptions.destroy")->middleware('permission:delete design options');
    });

    // Routes for orders management
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index')->middleware('permission:view all order');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('permission:view order');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:update status order');
        // Invoice Routes
        Route::prefix('/{order}/invoice')->group(function () {
            Route::get('/', [InvoiceController::class, 'show'])->name('orders.invoice')->middleware('permission:view invoice');
            Route::post('/regenerate', [InvoiceController::class, 'regenerate'])->name('orders.invoice.regenerate')->middleware('permission:regenerate invoice');
        });
    });

    // Routes for designs management
    Route::prefix('designs')->group(function () {
        Route::get("", [DesignController::class, "index"])->name("designs.index")->middleware('permission:view all designs');
        Route::get('{design}', [DesignController::class, 'show'])->name('designs.show')->middleware('permission:view designs');
        Route::get('{design}/edit', [DesignController::class, 'edit'])->name('designs.edit')->middleware('permission:update all designs');
        Route::put('{design}', [DesignController::class, 'update'])->name('designs.update')->middleware('permission:update all designs');
        Route::delete('{design}', [DesignController::class, 'destroy'])->name('designs.destroy')->middleware('permission:delete all designs');
    });

    Route::prefix('admin/wallet')->name('admin.wallet.')->group(function () {
        Route::get('/{user}', [WalletController::class, 'showUserWallet'])->name('show')->middleware('permission:view user wallet');
        Route::post('/{user}/deposit', [WalletController::class, 'deposit'])->name('deposit')->middleware('permission:deposit to wallet');
        Route::post('/{user}/withdraw', [WalletController::class, 'withdraw'])->name('withdraw')->middleware('permission:withdraw from wallet');
    });

    // Coupon Management Routes
    Route::prefix('coupons')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('coupons.index')->middleware('permission:view all coupon');
        Route::get('/create', [CouponController::class, 'create'])->name('coupons.create')->middleware('permission:create coupon');
        Route::post('/', [CouponController::class, 'store'])->name('coupons.store')->middleware('permission:create coupon');
        Route::get('/{coupon}', [CouponController::class, 'show'])->name('coupons.show')->middleware('permission:view coupon');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit')->middleware('permission:update coupon');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('coupons.update')->middleware('permission:update coupon');
        Route::post('/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle')->middleware('permission:update coupon');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy')->middleware('permission:delete coupon');
    });

    // Review Management Routes
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('reviews.index')->middleware('permission:view all review');
        Route::get('/stats', [ReviewController::class, 'stats'])->name('reviews.stats')->middleware('permission:view all review');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('permission:delete review');
    });

    // Super Admin Routes
    Route::middleware('role:super_admin')->group(function () {
        Route::prefix('super-admin')->group(function () {
            // Super Admin - Admin Management Routes
            Route::prefix('/admins')->name('super-admin.admins.')->group(function () {
                Route::get('/', [AdminManagementController::class, 'index'])->name('index')->middleware('permission:view all admin');
                Route::get('/create', [AdminManagementController::class, 'create'])->name('create')->middleware('permission:create admin');
                Route::post('/', [AdminManagementController::class, 'store'])->name('store')->middleware('permission:create admin');
                Route::get('/{admin}', [AdminManagementController::class, 'show'])->name('show')->middleware('permission:view admin');
                Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit')->middleware('permission:update admin');
                Route::put('/{admin}', [AdminManagementController::class, 'update'])->name('update')->middleware('permission:update admin');
                Route::delete('/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy')->middleware('permission:delete admin');
            });
            // Super Admin - Role Management Routes
            Route::prefix('/roles')->name('super-admin.roles.')->group(function () {
                Route::get('/', [RoleManagementController::class, 'index'])->name('index')->middleware('permission:view all role');
                Route::get('/create', [RoleManagementController::class, 'create'])->name('create')->middleware('permission:create role');
                Route::post('/', [RoleManagementController::class, 'store'])->name('store')->middleware('permission:create role');
                Route::get('/{role}', [RoleManagementController::class, 'show'])->name('show')->middleware('permission:view role');
                Route::get('/{role}/edit', [RoleManagementController::class, 'edit'])->name('edit')->middleware('permission:update role');
                Route::put('/{role}', [RoleManagementController::class, 'update'])->name('update')->middleware('permission:update role');
                Route::delete('/{role}', [RoleManagementController::class, 'destroy'])->name('destroy')->middleware('permission:delete role');
            });
        });
    });

});


