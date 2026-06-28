<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TablesController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/tables', [TablesController::class, 'index'])->name('tables');
