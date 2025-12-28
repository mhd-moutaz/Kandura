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

Route::get("", function () {
    return redirect()->route("login");
});

Route::get("login", [AuthController::class, "loginView"])->name("login");
Route::post("login", [AuthController::class, "login"])->name("login_action");
Route::get('stripe/order/success', [StripeController::class, 'orderSuccess'])->name('stripe.order.success');
Route::get('stripe/order/cancel', [StripeController::class, 'orderCancel'])->name('stripe.order.cancel');

Route::middleware(['auth', 'role:admin||super_admin'])->group(function () {
    Route::get("dashboard", [DashboardController::class, "index"])->name("home");
    Route::post("logout", [AuthController::class, "logout"])->name("logout");
    // Routes for users management
    Route::prefix('users')->group(function () {
        Route::get("", [UserController::class, "index"])->name("users.index")->middleware("permission:view all users");
        Route::get("{user}/edit", [UserController::class, "edit"])->name("users.edit")->middleware("permission:disable user");
        Route::put("{user}", [UserController::class, "update"])->name("users.update")->middleware("permission:disable user");
        Route::delete("{user}", [UserController::class, "destroy"])->name("users.destroy")->middleware("permission:delete user");
    });
    // Routes for addresses management
    Route::prefix('addresses')->group(function () {
        Route::get("", [AddressController::class, "index"])->name("addresses.index")->middleware('permission:view all address');

    });
    // Routes for design options management
    Route::prefix('designOptions')->middleware('permission:manage design options')->group(function () {
        Route::get("", [DesignOptionsController::class, "index"])->name("designOptions.index");
        Route::get('/create', [DesignOptionsController::class, 'create'])->name('designOptions.create');
        Route::post('/store', [DesignOptionsController::class, 'store'])->name('designOptions.store');
        Route::get("{designOption}/edit", [DesignOptionsController::class, "edit"])->name("designOptions.edit");
        Route::put("{designOption}", [DesignOptionsController::class, "update"])->name("designOptions.update");
        Route::delete("{designOption}", [DesignOptionsController::class, "destroy"])->name("designOptions.destroy");
    });
    // Routes for orders management
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index')->middleware('permission:view all orders');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('permission:view all orders');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:change order status');
    });
    // Routes for designs management
    Route::prefix('designs')->group(function () {
        Route::get("", [DesignController::class, "index"])->name("designs.index");
        Route::get('/create', [DesignController::class, 'create'])->name('designs.create');
        Route::post('/', [DesignController::class, 'store'])->name('designs.store');
        Route::get('{design}', [DesignController::class, 'show'])->name('designs.show');
        Route::get('{design}/edit', [DesignController::class, 'edit'])->name('designs.edit');
        Route::put('{design}', [DesignController::class, 'update'])->name('designs.update');
        Route::delete('{design}', [DesignController::class, 'destroy'])->name('designs.destroy');
    });
    Route::prefix('admin/wallet')->name('admin.wallet.')->group(function () {
        Route::get('/{user}', [WalletController::class, 'showUserWallet'])->name('show');
        Route::post('/{user}/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/{user}/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
    });
});


/* todo :
middleware('permission:manage wallet') => wallet routes
*/

