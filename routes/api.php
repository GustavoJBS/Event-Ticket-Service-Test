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

Route::name('api.')->group(function () {
    Route::apiResource('events', EventsController::class)
        ->only(['index', 'show']);

    Route::apiResource('reservations', ReservationController::class)
        ->only(['store', 'update', 'destroy']);
});
