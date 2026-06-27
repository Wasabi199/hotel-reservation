<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('/user')
    ->name('user.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::prefix('/reservations')
            ->name('reservations.')
            ->controller(ReservationController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{reservation}', 'show')->name('show');
                Route::delete('/{reservation}', 'archive')->name('archive');
                Route::patch('/{reservation}', 'restore')->name('restore')->withTrashed();
            });

        Route::get('/available-rooms', [DropdownController::class, 'availableRooms'])->name('available-rooms');
    });

require __DIR__.'/settings.php';
