<?php

use App\Http\Controllers\{EventsController, ReservationController};
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::fallback(
    fn () => response()->json([
        'status'  => false,
        'message' => trans('response.api_endpoint_not_found')
    ], Response::HTTP_NOT_FOUND)
);

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

    Route::put('/{reservation}', [ReservationController::class, 'update'])
        ->name('update');
});
