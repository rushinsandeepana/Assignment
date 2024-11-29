<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConcessionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //handle concessions
    Route::post('/admin/view-concessions', [ConcessionsController::class, 'view'])->name('concession.view');
});

require __DIR__.'/auth.php';