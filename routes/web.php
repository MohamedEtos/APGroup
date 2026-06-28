<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\OfficeInvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/tables', [TablesController::class, 'index'])->name('tables');

    Route::get('/office-invoices', [OfficeInvoiceController::class, 'index'])->name('office-invoices.index');
    Route::post('/office-invoices', [OfficeInvoiceController::class, 'store'])->name('office-invoices.store');
    Route::get('/invoice-receipt/{id}', [OfficeInvoiceController::class, 'showInvoice'])->name('invoice-receipt.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
