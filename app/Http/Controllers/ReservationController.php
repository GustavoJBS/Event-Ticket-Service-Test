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
                    'entity' => trans('entities.reservation')
                ]),
                'data' => $reservation
            ],
            status: Response::HTTP_CREATED
        );
    }

    public function update(UpdateRequest $updateRequest, Reservation $reservation)
    {
        $reservation->update(array_filter($updateRequest->validated()));

        return response()->json(
            [
                'status'  => true,
                'message' => trans('response.updated', [
                    'entity' => trans('entities.reservation')
                ]),
                'data' => $reservation
            ],
            status: Response::HTTP_OK
        );
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $reservation->delete();

        return response()->json(
            [
                'status'  => true,
                'message' => trans('response.cancel', [
                    'entity' => trans('entities.reservation')
                ])
            ],
            status: Response::HTTP_OK
        );
    }
}
