<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\AddressController;
use App\Http\Controllers\Users\DesignController;


// Authentication routes -------------
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('designs/allDesigns', [DesignController::class, 'allDesigns']);

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    // User routes --------------
    Route::prefix('users')->group(function () {
        // Profile routes ------------
        Route::get('/', [UserController::class, 'show'])->middleware('permission:view profile');
        Route::put('/', [UserController::class, 'update'])->middleware('permission:update profile');
        Route::delete('/', [UserController::class, 'destroy'])->middleware('permission:delete profile');
        // Address routes ------------
        Route::prefix('address')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->middleware('permission:view address');
            Route::post('/', [AddressController::class, 'store'])->middleware('permission:create address');
            Route::put('/{address}', [AddressController::class, 'update'])->middleware('permission:update address');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->middleware('permission:delete address');
        });
        // Design routes ------------
        Route::prefix('designs')->group(function () {
            Route::get('/myDesigns', [DesignController::class, 'myDesigns'])->middleware('permission:view design');
            Route::post('/', [DesignController::class, 'store'])->middleware('permission:create design');
            Route::put('/{design}', [DesignController::class, 'update'])->middleware('permission:update design');
            Route::delete('/{design}', [DesignController::class, 'destroy'])->middleware('permission:delete design');
        });
    });
});
