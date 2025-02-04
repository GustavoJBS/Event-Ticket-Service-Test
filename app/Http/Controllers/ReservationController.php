<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservations\{StoreRequest, UpdateRequest};
use App\Models\{Event, Reservation};
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(StoreRequest $storeRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            Event::query()
                ->lockForUpdate()
                ->find($storeRequest->validated('event_id'));

            $reservation = Reservation::create($storeRequest->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return jsonResponse(
                status: false,
                message: $th->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST,
                errorMessage: trans('response.failed_to_create', [
                    'entity' => trans('entities.reservation')
                ])
            );
        }

        return jsonResponse(
            status: true,
            message: trans('response.created', [
                'entity' => trans('entities.reservation')
            ]),
            statusCode: Response::HTTP_CREATED,
            data: $reservation->toArray()
        );
    }

    public function update(UpdateRequest $updateRequest, Reservation $reservation): JsonResponse
    {
        DB::beginTransaction();

        try {
            Event::query()
                ->lockForUpdate()
                ->find($reservation->event_id);

            $reservation->update(array_filter($updateRequest->validated()));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return jsonResponse(
                status: false,
                message: $th->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST,
                errorMessage: trans('response.failed_to_update', [
                    'entity' => trans('entities.reservation')
                ])
            );
        }

        return jsonResponse(
            status: true,
            message: trans('response.updated', [
                'entity' => trans('entities.reservation')
            ]),
            statusCode: Response::HTTP_OK,
            data: $reservation->toArray()
        );
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $reservation->delete();

        return jsonResponse(
            status: true,
            message: trans('response.cancel', [
                'entity' => trans('entities.reservation')
            ]),
            statusCode: Response::HTTP_OK
        );
    }
}
