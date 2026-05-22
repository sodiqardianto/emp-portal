<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// App
Route::middleware('auth')->group(function (): void {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::prefix('menu/system/permissions')
        ->middleware('menu.access:/menu/system/permissions')
        ->group(function (): void {
            Route::get('/', [PermissionController::class, 'index'])
                ->middleware('menu.permission:VIEW')
                ->name('permissions.index');
            Route::get('/data', [PermissionController::class, 'data'])
                ->middleware('menu.permission:VIEW')
                ->name('permissions.data');
            Route::post('/', [PermissionController::class, 'store'])
                ->middleware('menu.permission:ADD')
                ->name('permissions.store');
            Route::put('/{code}', [PermissionController::class, 'update'])
                ->middleware('menu.permission:EDIT')
                ->name('permissions.update');
            Route::delete('/{code}', [PermissionController::class, 'destroy'])
                ->middleware('menu.permission:DELETE')
                ->name('permissions.destroy');
        });
});
