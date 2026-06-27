<?php

use App\Http\Controllers\Api\ReservationController;
use App\Http\Middleware\ApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware(ApiKey::class)->group(function () {
    Route::prefix('/reservations')
        ->name('api.reservations.')
        ->controller(ReservationController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('/{reservation}', 'show')->name('show');
        });

    Route::get('/hotels/{hotel}/available-rooms', [ReservationController::class, 'availableRooms'])->name('api.hotels.available-rooms');
});
