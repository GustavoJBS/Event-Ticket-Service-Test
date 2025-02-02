<?php

use App\Models\{Event, Reservation};

use function Pest\Laravel\{delete, post, put};

it('should avoid to create a reservation if it has more tickets than available', function () {
    Reservation::factory()->create([
        'number_of_tickets' => 1001,
        'event_id'          => Event::factory()->create(['total_availability' => 1000])
    ]);
})->throws(Exception::class, 'This event has only 1000 tickets remaining.');

it('should avoid to create a reservation if the event has no tickets available', function () {
    Reservation::factory()->create([
        'number_of_tickets' => 5000,
        'event_id'          => Event::factory()->create(['total_availability' => 0])
    ]);
})->throws(Exception::class, 'There are no tickets available for this event.');

it('should validate reservations store parameters', function () {
    post(route('api.reservations.store'))
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'event_id'          => ['The event id field is required.'],
                'number_of_tickets' => ['The number of tickets field is required.']
            ]
        ]);

    post(route('api.reservations.store'), [
        'event_id'          => PHP_INT_MAX,
        'number_of_tickets' => PHP_INT_MAX
    ])
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'event_id'          => ['The selected event id is invalid.'],
                'number_of_tickets' => ['Not enough tickets available. Please reduce the number of tickets you\'ve selected. There are only 0 tickets remaining.']
            ]
        ]);

    $event = Event::factory()->create();

    post(route('api.reservations.store'), [
        'event_id'          => $event->id,
        'number_of_tickets' => PHP_INT_MAX
    ])
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'number_of_tickets' => [
                    trans('validation.max_number_of_tickets', [
                        'max' => $event->remaining_availability
                    ])
                ]
            ]
        ]);
});

it('should create a new reservation', function () {
    $event = Event::factory()->create([
        'total_availability' => 100
    ]);

    post(route('api.reservations.store'), [
        'event_id'          => $event->id,
        'number_of_tickets' => 10
    ])
        ->assertCreated()
        ->assertJson([
            'status'  => true,
            'message' => trans('response.created', [
                'entity' => trans('entities.reservation')
            ]),
            'data' => [
                'event_id'          => $event->id,
                'number_of_tickets' => 10
            ]
        ]);

    $event->refresh();

    expect($event->remaining_availability)
        ->toBe(90);
});

it(
    'should not update reservation if id is invalid',
    fn () => put(route('api.reservations.update', ['reservation' => PHP_INT_MAX]))
        ->assertNotFound()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.not_found', [
                'entity' => trans('entities.reservation')
            ]),
        ])
);

it('should validate reservations update parameters', function () {
    $reservation = Reservation::factory()->create([
        'number_of_tickets' => 20
    ]);

    $reservation->refresh();

    put(route('api.reservations.update', ['reservation' => $reservation->id]))
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'number_of_tickets' => ['The number of tickets field is required.']
            ]
        ]);

    put(
        route('api.reservations.update', ['reservation' => $reservation->id]),
        ['number_of_tickets' => -1]
    )
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'number_of_tickets' => ['The number of tickets field must be at least 1.']
            ]
        ]);

    put(
        route('api.reservations.update', ['reservation' => $reservation->id]),
        ['number_of_tickets' => fake()->randomNumber(5)]
    )
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => [
                'number_of_tickets' => [
                    trans('validation.max_number_of_tickets', [
                        'max' => $reservation->event->total_availability
                    ])
                ]
            ]
        ]);
});

it('should update reservation number of tickets', function () {
    $event = Event::factory()->create([
        'total_availability' => 100
    ]);

    $reservation = Reservation::factory()->create([
        'number_of_tickets' => 20,
        'event_id'          => $event->id
    ]);

    $event->refresh();

    expect($event->remaining_availability)
        ->toBe(80);

    put(
        route('api.reservations.update', ['reservation' => $reservation->id]),
        ['number_of_tickets' => 10]
    )
        ->assertOk()
        ->assertJson([
            'status'  => true,
            'message' => trans('response.updated', [
                'entity' => trans('entities.reservation')
            ]),
            'data' => [
                'id'                => $reservation->id,
                'number_of_tickets' => 10
            ]
        ]);

    $event->refresh();

    expect($event->remaining_availability)
        ->toBe(90);
});

it(
    'should not destroy a reservation if id is invalid',
    fn () => delete(route('api.reservations.destroy', ['reservation' => PHP_INT_MAX]))
        ->assertNotFound()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.not_found', [
                'entity' => trans('entities.reservation')
            ]),
        ])
);

it('should destroy a reservation', function () {
    $event = Event::factory()->create([
        'total_availability' => 100
    ]);

    $reservation = Reservation::factory()->create([
        'number_of_tickets' => 20,
        'event_id'          => $event->id
    ]);

    delete(route('api.reservations.destroy', ['reservation' => $reservation]))
        ->assertOk()
        ->assertJson([
            'status'  => true,
            'message' => trans('response.cancel', [
                'entity' => trans('entities.reservation')
            ])
        ]);

    $event->refresh();

    expect($event->remaining_availability)
        ->toBe(100);
});
