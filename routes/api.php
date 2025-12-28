<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DaretApiController;
use App\Http\Controllers\Api\ContributionApiController;
use App\Http\Controllers\Api\ProfileApiController;

Route::middleware(['auth'])->group(function () {
    Route::get('/darets', [DaretApiController::class, 'index']);
    Route::get('/darets/{daret}', [DaretApiController::class, 'show']);
    Route::post('/darets', [DaretApiController::class, 'store']);
    Route::post('/darets/{daret}/join', [DaretApiController::class, 'join']);

    Route::get('/darets/{daret}/contributions', [ContributionApiController::class, 'index']);
    Route::post('/darets/{daret}/contributions/{cycle}/upload', [ContributionApiController::class, 'uploadReceipt']);
    Route::post('/contributions/{contribution}/confirm', [ContributionApiController::class, 'confirm']);
    Route::post('/contributions/{contribution}/reject', [ContributionApiController::class, 'reject']);

    Route::get('/profiles/{user}', [ProfileApiController::class, 'show']);
});
