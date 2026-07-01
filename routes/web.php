<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\OfficeInvoiceController;
use App\Http\Controllers\ReceiveInvoiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {
    // User & Role Management
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/roles/{id}/permissions', [UserController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    });
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tables', [TablesController::class, 'index'])
        ->middleware('permission:view reports')
        ->name('tables');

    Route::middleware('permission:create invoices')->group(function () {
        Route::get('/office-invoices', [OfficeInvoiceController::class, 'index'])->name('office-invoices.index');
        Route::post('/office-invoices', [OfficeInvoiceController::class, 'store'])->name('office-invoices.store');
    });

    Route::middleware('permission:edit invoices')->group(function () {
        Route::get('/office-invoices/{id}/edit', [OfficeInvoiceController::class, 'edit'])->name('office-invoices.edit');
        Route::put('/office-invoices/{id}', [OfficeInvoiceController::class, 'update'])->name('office-invoices.update');
    });

    Route::delete('/office-invoices/{id}', [OfficeInvoiceController::class, 'destroy'])
        ->middleware('permission:delete invoices')
        ->name('office-invoices.destroy');

    Route::get('/invoice-receipt/{id}', [OfficeInvoiceController::class, 'showInvoice'])
        ->middleware('permission:view reports')
        ->name('invoice-receipt.show');

    Route::middleware('permission:receive invoices')->group(function () {
        Route::get('/receive-invoices', [ReceiveInvoiceController::class, 'index'])->name('receive-invoices.index');
        Route::get('/receive-invoices/{id}', [ReceiveInvoiceController::class, 'show'])->name('receive-invoices.show');
        Route::put('/receive-invoices/{id}', [ReceiveInvoiceController::class, 'update'])->name('receive-invoices.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
