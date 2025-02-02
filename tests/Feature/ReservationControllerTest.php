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

it('should validate reservations store parameters', function (
    array $data,
    array $errors,
    bool $createEvent = false
) {

    if ($createEvent) {
        $data['event_id'] = Event::factory()->create([
            'total_availability' => 100
        ])->id;
    }

    post(route('api.reservations.store'), $data)
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => $errors
        ]);
})->with('reservation-store-validations');

it('should create a new reservation', function () {
    $event = Event::factory()->create([
        'total_availability' => 100
    ]);

    $reservationHolder = fake()->name;

    post(route('api.reservations.store'), [
        'event_id'           => $event->id,
        'number_of_tickets'  => 10,
        'reservation_holder' => $reservationHolder
    ])
        ->assertCreated()
        ->assertJson([
            'status'  => true,
            'message' => trans('response.created', [
                'entity' => trans('entities.reservation')
            ]),
            'data' => [
                'event_id'           => $event->id,
                'number_of_tickets'  => 10,
                'reservation_holder' => $reservationHolder
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

it('should validate reservations update parameters', function (array $data, array $errors, int $numberOfTickets = 20) {
    $reservation = Reservation::factory()->create([
        'number_of_tickets' => $numberOfTickets,
        'event_id'          => Event::factory()->create(['total_availability' => 100])->id
    ]);

    put(
        route('api.reservations.update', ['reservation' => $reservation->id]),
        $data
    )
        ->assertUnprocessable()
        ->assertJson([
            'status'  => false,
            'message' => trans('response.invalid_paramaters'),
            'errors'  => $errors
        ]);
})->with('reservation-update-validations');

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
