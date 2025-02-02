<?php

use App\Http\Requests\Reservations\UpdateRequest;
use App\Models\{Event, Reservation};

use function Pest\Laravel\{partialMock, put};

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

it('should fail to update a reservation', function () {
    $event = Event::factory()->create([
        'total_availability' => 30
    ]);

    $reservation = Reservation::factory()->create([
        'number_of_tickets' => 10,
        'event_id'          => $event->id
    ]);

    partialMock(UpdateRequest::class)
        ->shouldReceive('validated')
        ->withAnyArgs()
        ->andReturn([
            'number_of_tickets' => 50
        ]);

    put(route('api.reservations.update', ['reservation' => $reservation->id]), [
        'number_of_tickets' => 10,
    ])
        ->assertBadRequest()
        ->assertJson([
            'status' => true,
            'error'  => trans('response.failed_to_update', [
                'entity' => trans('entities.reservation')
            ]),
            'message' => trans('exception.tickets_available_not_enough', [
                'total' => 20,
                'label' => trans('entities.tickets')
            ])
        ]);
});
