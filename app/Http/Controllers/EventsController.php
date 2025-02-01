<?php

namespace App\Http\Controllers;

use App\Http\Requests\Events\FilterRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EventsController extends Controller
{
    public function index(FilterRequest $filterRequest): JsonResponse
    {
        $filters       = $filterRequest->validated('filters', []);
        $perPage       = $filterRequest->validated('perPage', 10);
        $sortBy        = $filterRequest->validated('sortBy', 'id');
        $sortDirection = $filterRequest->validated('sortDirection', 'asc');

        $events = Event::query()
            ->filters($filters)
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return response()->json(
            data: array_merge(
                [
                    'status'  => true,
                    'message' => trans('response.retrieved', [
                        'entity' => 'Events'
                    ])
                ],
                $events->toArray()
            ),
            status: Response::HTTP_OK
        );
    }

    public function show(Event $event): JsonResponse
    {
        $event->load('reservations');

        return response()->json(
            data: [
                'status'  => true,
                'message' => trans('response.retrieved', [
                    'entity' => 'Event'
                ]),
                'data' => $event
            ],
            status: Response::HTTP_OK
        );
    }
}
