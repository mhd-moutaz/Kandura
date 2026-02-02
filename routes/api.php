<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\OrderController;
use App\Http\Controllers\Users\DesignController;
use App\Http\Controllers\Users\WalletController;
use App\Http\Controllers\Users\AddressController;
use App\Http\Controllers\Users\OrderItemsController;
use App\Http\Controllers\Users\StripeWebhookController;
use App\Http\Controllers\Users\CouponController;
use App\Http\Controllers\Users\ReviewController;
use App\Http\Controllers\Users\InvoiceController;

// Authentication routes -------------
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('designs/allDesigns', [DesignController::class, 'allDesigns']);

// Stripe Webhook (يجب أن يكون خارج middleware لأن Stripe يرسل البيانات بدون authentication)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    // User routes
    Route::prefix('users')->group(function () {

        // Profile routes
        Route::prefix('profile')->group(function () {
            Route::get('/', [UserController::class, 'show'])->middleware('permission:view profile');
            Route::put('/', [UserController::class, 'update'])->middleware('permission:update profile');
            Route::delete('/', [UserController::class, 'destroy'])->middleware('permission:delete profile');
        });

        // Address routes
        Route::prefix('address')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->middleware('permission:view address');
            Route::post('/', [AddressController::class, 'store'])->middleware('permission:create address');
            Route::put('/{address}', [AddressController::class, 'update'])->middleware('permission:update address');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->middleware('permission:delete address');
        });

        // Design routes
        Route::prefix('designs')->group(function () {
            Route::get('/myDesigns', [DesignController::class, 'show'])->middleware('permission:view designs');
            Route::post('/', [DesignController::class, 'store'])->middleware('permission:create designs');
            Route::put('/{design}', [DesignController::class, 'update'])->middleware('permission:update designs');
            Route::delete('/{design}', [DesignController::class, 'destroy'])->middleware('permission:delete designs');
        });

        // Order Items routes
        Route::prefix('order-items')->group(function () {
            Route::post('/', [OrderItemsController::class, 'store'])->middleware('permission:create order');
            Route::put('/{orderItem}', [OrderItemsController::class, 'update'])->middleware('permission:update order');
            Route::delete('/{orderItem}', [OrderItemsController::class, 'destroy'])->middleware('permission:delete order');
        });

        // Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->middleware('permission:view order');
            Route::get('/pending', [OrderController::class, 'getPending'])->middleware('permission:view order');
            Route::get('/{order}', [OrderController::class, 'show'])->middleware('permission:view order');
            Route::put('/{order}/confirm', [OrderController::class, 'confirmOrder'])->middleware('permission:update order');
            // Coupon routes for orders
            Route::post('/{order}/coupon/apply', [CouponController::class, 'apply'])->middleware('permission:apply coupon');
            Route::delete('/{order}/coupon/remove', [CouponController::class, 'remove'])->middleware('permission:remove coupon');
            Route::post('/{order}/coupon/validate', [CouponController::class, 'validate'])->middleware('permission:validate coupon');
            // Invoice route for orders
            Route::get('/{order}/invoice', [InvoiceController::class, 'show'])->middleware('permission:view invoice');
        });

        // Wallet routes
        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'getBalance'])->middleware('permission:view wallet');
            Route::get('/transactions', [WalletController::class, 'getTransactions'])->middleware('permission:view transactions');
        });

        // Review routes
        Route::prefix('reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->middleware('permission:view review');
            Route::post('/orders/{order}', [ReviewController::class, 'store'])->middleware('permission:create review');
            // Route::delete('/{review}', [ReviewController::class, 'destroy'])->middleware('permission:view review');
        });
    });
});
