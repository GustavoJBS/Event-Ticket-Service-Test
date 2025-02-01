<?php

use App\Models\{Event, Reservation};
use Illuminate\Http\Response;

use function Pest\Laravel\get;

it('should validate events index parameters', function () {
    $response = get(route('events.index', [
        'page'          => fake()->word,
        'perPage'       => fake()->word,
        'sortBy'        => fake()->name,
        'sortDirection' => fake()->name,
        'filters'       => [
            'name'           => [fake()->word],
            'description'    => [fake()->word],
            'only_available' => fake()->randomNumber,
            'start_date'     => fake()->randomNumber,
            'end_date'       => fake()->randomNumber,
        ],
    ]));

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'page'                   => ['The page field must be a number.'],
                'perPage'                => ['The per page field must be a number.'],
                'sortBy'                 => ['The selected sort by is invalid.'],
                'sortDirection'          => ['The selected sort direction is invalid.'],
                'filters.name'           => ['The name field must be a string.'],
                'filters.description'    => ['The description field must be a string.'],
                'filters.only_available' => ['The only available field must be true or false.'],
                'filters.start_date'     => ['The start date field must be a valid date.'],
                'filters.end_date'       => ['The end date field must be a valid date.'],
            ]
        ]);
});

it('should list events by page', function () {
    Event::factory(15)->create();

    get(route('events.index'))
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(10, 'data')
        ->assertJsonCount(4, 'links')
        ->assertJson([
            'message' => trans('response.retrieved', [
                'entity' => 'Events'
            ]),
            'status'       => true,
            'total'        => 15,
            'per_page'     => 10,
            'current_page' => 1,
            'from'         => 1,
            'to'           => 10
        ]);

    get(route('events.index', ['page' => 2]))
        ->assertStatus(Response::HTTP_OK)
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

it(
    'should not show event if id is invalid',
    fn () => get(route('events.show', ['event' => PHP_INT_MAX]))
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJson([
            'status'  => false,
            'message' => trans('response.not_found', [
                'entity' => 'Event'
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

    get(route('events.show', ['event' => $event->id]))
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'status'  => true,
            'message' => trans('response.retrieved', [
                'entity' => 'Event'
            ]),
            'data' => $event->toArray()
        ]);
});
