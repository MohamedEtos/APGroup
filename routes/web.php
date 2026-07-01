<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\OfficeInvoiceController;
use App\Http\Controllers\ReceiveInvoiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/tables', [TablesController::class, 'index'])->name('tables');

    Route::get('/office-invoices', [OfficeInvoiceController::class, 'index'])->name('office-invoices.index');
    Route::post('/office-invoices', [OfficeInvoiceController::class, 'store'])->name('office-invoices.store');
    Route::get('/office-invoices/{id}/edit', [OfficeInvoiceController::class, 'edit'])->name('office-invoices.edit');
    Route::put('/office-invoices/{id}', [OfficeInvoiceController::class, 'update'])->name('office-invoices.update');
    Route::get('/invoice-receipt/{id}', [OfficeInvoiceController::class, 'showInvoice'])->name('invoice-receipt.show');

    Route::get('/receive-invoices', [ReceiveInvoiceController::class, 'index'])->name('receive-invoices.index');
    Route::get('/receive-invoices/{id}', [ReceiveInvoiceController::class, 'show'])->name('receive-invoices.show');
    Route::put('/receive-invoices/{id}', [ReceiveInvoiceController::class, 'update'])->name('receive-invoices.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
