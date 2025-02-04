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

        return jsonResponse(
            status: true,
            message: trans('response.retrieved', [
                'entity' => trans('entities.events')
            ]),
            statusCode: Response::HTTP_OK,
            mergeData: $events->toArray()
        );
    }

    public function show(Event $event): JsonResponse
    {
        $event->load('reservations');

        return jsonResponse(
            status: true,
            message: trans('response.retrieved', [
                'entity' => trans('entities.event')
            ]),
            statusCode: Response::HTTP_OK,
            data: $event->toArray()
        );
    }
}
