<?php

use App\Models\{Event, Reservation};

use function Pest\Laravel\get;

it('should validate events index parameters', function (array $data, array $errors) {
    $response = get(route('api.events.index', $data));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => $errors
        ]);
})->with('events-filter-validations');

it('should list events by page', function () {
    Event::factory(15)->create();

    get(route('api.events.index'))
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonCount(4, 'links')
        ->assertJson([
            'message' => trans('response.retrieved', [
                'entity' => trans('entities.events')
            ]),
            'status'       => true,
            'total'        => 15,
            'per_page'     => 10,
            'current_page' => 1,
            'from'         => 1,
            'to'           => 10
        ]);

    get(route('api.events.index', ['page' => 2]))
        ->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonCount(4, 'links')
        ->assertJson([
            'status'       => true,
            'total'        => 15,
            'per_page'     => 10,
            'current_page' => 2,
            'from'         => 11,
            'to'           => 15
        ]);
});

it('should filter list of events', function (array $filters) {
    Event::factory(5)->create([
        'date'               => now()->addMonth()->format('Y-m-d'),
        'total_availability' => 10,
    ]);

    Event::factory(5)->create([
        'name'               => $filters['name'] ?? fake()->name,
        'date'               => $filters['start_date'] ?? $filters['end_date'] ?? now()->format('Y-m-d'),
        'description'        => $filters['description'] ?? fake()->sentence,
        'total_availability' => 0,
    ]);

    get(route('api.events.index', ['filters' => $filters]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
})->with('events-filters');

it(
    'should not show event if id is invalid',
    fn () => get(route('api.events.show', ['event' => PHP_INT_MAX]))
        ->assertNotFound()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.not_found', [
                'entity' => trans('entities.event')
            ]),
        ])
);

it('should show event with reservations', function () {
    $event = Event::factory()->create([
        'total_availability' => 10,
    ]);

    Reservation::factory(5)->create([
        'event_id'          => $event->id,
        'number_of_tickets' => 1
    ]);

    $event->refresh();

    $event->load('reservations');

    get(route('api.events.show', ['event' => $event->id]))
        ->assertOk()
        ->assertJson([
            'status'  => true,
            'message' => trans('response.retrieved', [
                'entity' => trans('entities.event')
            ]),
            'data' => $event->toArray()
        ]);
});
