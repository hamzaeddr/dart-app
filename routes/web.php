<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DaretController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profiles.show');

    Route::get('/darets', [DaretController::class, 'index'])->name('darets.index');
    Route::get('/darets/create', [DaretController::class, 'create'])->name('darets.create');
    Route::post('/darets', [DaretController::class, 'store'])->name('darets.store');
    Route::get('/darets/{daret}', [DaretController::class, 'show'])->name('darets.show');
    Route::post('/darets/{daret}/join', [DaretController::class, 'join'])->name('darets.join');
    Route::post('/darets/{daret}/add-member', [DaretController::class, 'addMember'])->name('darets.add-member');
    Route::post('/darets/{daret}/update-recipient-order', [DaretController::class, 'updateRecipientOrder'])->name('darets.update-recipient-order');

    Route::get('/darets/{daret}/cycles/{cycle}', [ContributionController::class, 'showCycle'])
        ->name('darets.cycles.show');
    Route::post('/darets/{daret}/cycles/{cycle}/upload', [ContributionController::class, 'uploadReceipt'])
        ->name('contributions.upload');
    Route::post('/contributions/{contribution}/confirm', [ContributionController::class, 'confirm'])
        ->name('contributions.confirm');
    Route::post('/contributions/{contribution}/reject', [ContributionController::class, 'reject'])
        ->name('contributions.reject');
    Route::get('/contributions/{contribution}/receipt', [ContributionController::class, 'viewReceipt'])
        ->name('contributions.receipt');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';
