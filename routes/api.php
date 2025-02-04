<?php

use App\Http\Controllers\{EventsController, ReservationController};
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::fallback(fn () => jsonResponse(
    status: false,
    message: trans('response.api_endpoint_not_found'),
    statusCode: Response::HTTP_NOT_FOUND
));

Route::name('api.')->group(function () {
    Route::apiResource('events', EventsController::class)
        ->only(['index', 'show']);

    Route::apiResource('reservations', ReservationController::class)
        ->only(['store', 'update', 'destroy']);
});
