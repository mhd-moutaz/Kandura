<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\OrderController;
use App\Http\Controllers\Users\DesignController;
use App\Http\Controllers\Users\StripeController;
use App\Http\Controllers\Users\WalletController;
use App\Http\Controllers\Users\AddressController;
use App\Http\Controllers\Users\OrderItemsController;
use App\Http\Controllers\Users\StripeWebhookController;


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
        Route::get('/', [UserController::class, 'show'])->middleware('permission:view profile');
        Route::put('/', [UserController::class, 'update'])->middleware('permission:update profile');
        Route::delete('/', [UserController::class, 'destroy'])->middleware('permission:delete profile');

        // Address routes
        Route::prefix('address')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->middleware('permission:view address');
            Route::post('/', [AddressController::class, 'store'])->middleware('permission:create address');
            Route::put('/{address}', [AddressController::class, 'update'])->middleware('permission:update address');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->middleware('permission:delete address');
        });

        // Design routes
        Route::prefix('designs')->group(function () {
            Route::get('/myDesigns', [DesignController::class, 'myDesigns'])->middleware('permission:view design');
            Route::post('/', [DesignController::class, 'store'])->middleware('permission:create design');
            Route::put('/{design}', [DesignController::class, 'update'])->middleware('permission:update design');
            Route::delete('/{design}', [DesignController::class, 'destroy'])->middleware('permission:delete design');
        });

        // Order Items routes
        Route::prefix('order-items')->group(function () {
            Route::post('/', [OrderItemsController::class, 'store'])->middleware('permission:create order');
            Route::put('/{orderItem}', [OrderItemsController::class, 'update'])->middleware('permission:create order');
            Route::delete('/{orderItem}', [OrderItemsController::class, 'destroy'])->middleware('permission:create order');
        });

        // Order routes
        Route::prefix('orders')->middleware('permission:view order')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->middleware('permission:view order');
            Route::get('/pending', [OrderController::class, 'getPending']);
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::put('/{order}/confirm', [OrderController::class, 'confirmOrder']);
        });

        // Wallet routes
        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'getBalance'])->middleware('permission:view wallet');
            Route::get('/transactions', [WalletController::class, 'getTransactions'])->middleware('permission:view transactions');
        });

        // Stripe routes
        Route::prefix('stripe')->group(function () {
            Route::post('/order/{order}/checkout', [StripeController::class, 'createOrderCheckout'])->middleware('permission:create order');
        });
    });
});
// 'update order',
// 'delete order',
