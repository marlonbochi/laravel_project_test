<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/users', [UserController::class, 'index'])->name('users.index');
    Route::get('dashboard/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('dashboard/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('dashboard/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('dashboard/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('dashboard/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('dashboard/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('dashboard/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('dashboard/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
});

require __DIR__.'/settings.php';
