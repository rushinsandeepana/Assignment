<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConcessionsController;
use App\Http\Controllers\KitchenManagementController;
use App\Http\Controllers\OrderManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
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
    Route::delete('/concession/{id}', [ConcessionsController::class, 'delete'])->name('concession.delete');

    //handle order management
    Route::get('/admin/add-order', [OrderManagementController::class, 'add'])->name('order.add');
    Route::get('/admin/add-order', [OrderManagementController::class, 'index'])->name('order.index');
    Route::post('/orders', [OrderManagementController::class, 'create'])->name('order.store');
    Route::get('/orders', [OrderManagementController::class, 'viewAll'])->name('order.view');

    //handle kitchen management
    Route::get('/kitchen-view', [KitchenManagementController::class, 'view'])->name('kitchen.view');
    Route::post('/send-orders', [KitchenManagementController::class, 'sendToKitchen']);
    Route::post('/order/{orderId}/complete', [KitchenManagementController::class, 'markAsCompleted'])->name('order.complete');
    Route::delete('/delete-order/{orderId}', [KitchenManagementController::class, 'deleteOrder'])->name('orders.delete');




});

require __DIR__.'/auth.php';