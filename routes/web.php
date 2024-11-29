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
    Route::get('/admin/view-concessions', [ConcessionsController::class, 'show'])->name('concession.view');
    Route::get('/admin/add-concessions', [ConcessionsController::class, 'add'])->name('concession.add');
    Route::post('/admin/add-concessions', [ConcessionsController::class, 'store'])->name('concession.store');
    Route::get('concession/{id}/edit', [ConcessionsController::class, 'edit'])->name('concession.edit');
    Route::put('concession/{id}', [ConcessionsController::class, 'update'])->name('concession.update');

});

require __DIR__.'/auth.php';