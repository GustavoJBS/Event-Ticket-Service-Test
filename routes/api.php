<?php

use App\Http\Controllers\{EventsController, ReservationController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::fallback(function () {
    return response()->json([
        'error' => true,
    ], 400);
});

Route::group([
    'prefix' => 'events',
    'as'     => 'events'
], function () {
    Route::get('/', [EventsController::class, 'index'])
        ->name('index');

    Route::get('/{event}', [EventsController::class, 'show'])
        ->name('show');
});

Route::group([
    'prefix' => 'registrations',
    'as'     => 'registrations'
], function () {
    Route::post('/', [ReservationController::class, 'store'])
        ->name('store');

    Route::post('/{reservation}', [ReservationController::class, 'update'])
        ->name('update');
});
