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

            return response()->json(
                [
                    'status' => true,
                    'error'  => trans('response.failed_to_create', [
                        'entity' => trans('entities.reservation')
                    ]),
                    'message' => $th->getMessage(),
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }

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

            return response()->json(
                [
                    'status' => true,
                    'error'  => trans('response.failed_to_update', [
                        'entity' => trans('entities.reservation')
                    ]),
                    'message' => $th->getMessage(),
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }

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
