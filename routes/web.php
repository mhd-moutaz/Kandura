<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admins\AuthController;
use App\Http\Controllers\Admins\DashboardController;
use App\Http\Controllers\Admins\UserController;
use App\Http\Controllers\Admins\AddressController;

Route::get("", function () {
    return redirect()->route("login");
});


Route::get("login", [AuthController::class, "loginView"])->name("login");
Route::post("login", [AuthController::class, "login"])->name("login_action");

Route::middleware(['auth'])->group(function () {
    Route::get("dashboard", [DashboardController::class, "index"])->name("home");
    // Routes for users management
    Route::prefix('users')->group(function () {
        Route::get("", [UserController::class, "index"])->name("users.index");
        Route::get("{user}/edit", [UserController::class, "edit"])->name("users.edit");
        Route::put("{user}", [UserController::class, "update"])->name("users.update");
        Route::delete("{user}", [UserController::class, "destroy"])->name("users.destroy");
    });
    Route::prefix('addresses')->group(function () {
        Route::get("", [AddressController::class, "index"])->name("addresses.index");
        Route::get("{address}/edit", [AddressController::class, "edit"])->name("addresses.edit");
        Route::put("{address}", [AddressController::class, "update"])->name("addresses.update");
        Route::delete("{address}", [AddressController::class, "destroy"])->name("addresses.destroy");
    });
    Route::post("logout", [AuthController::class, "logout"])->name("logout");
});
