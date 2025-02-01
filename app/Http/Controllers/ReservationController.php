<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservations\{StoreRequest, UpdateRequest};
use App\Models\Reservation;
use Illuminate\Http\{JsonResponse, Response};

class ReservationController extends Controller
{
    public function store(StoreRequest $storeRequest): JsonResponse
    {
        $reservation = Reservation::create($storeRequest->validated());

        return response()->json(
            [
                'status'  => true,
                'message' => trans('response.created', [
                    'entity' => 'Reservation'
                ]),
                'data' => $reservation
            ],
            status: Response::HTTP_CREATED
        );
    }

    public function update(UpdateRequest $updateRequest, Reservation $reservation)
    {
        $reservation->update($updateRequest->validated());

        return response()->json(
            [
                'status'  => true,
                'message' => trans('response.updated', [
                    'entity' => 'Reservation'
                ]),
                'data' => $reservation
            ],
            status: Response::HTTP_OK
        );
    }

    public function destroy(Reservation $reservation)
    {
        //
    }
}
