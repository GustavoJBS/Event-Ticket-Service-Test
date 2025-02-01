<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservations\StoreRequest;
use App\Models\Reservation;
use Illuminate\Http\{JsonResponse, Response};

class ReservationController extends Controller
{
    public function store(StoreRequest $storeRequest): JsonResponse
    {
        $reservation = Reservation::create($storeRequest->validated());

        return response()
            ->json(
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

    public function update(Request $request, Reservation $reservation)
    {

    }

    public function destroy(Reservation $reservation)
    {
        //
    }
}
