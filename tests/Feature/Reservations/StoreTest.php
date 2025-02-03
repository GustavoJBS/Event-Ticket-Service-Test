<?php

use App\Http\Requests\Reservations\StoreRequest;
use App\Models\Event;

use function Pest\Laravel\{partialMock, post};

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

it('should fail to create a reservation', function () {
    $event = Event::factory()->create([
        'total_availability' => 30
    ]);

    partialMock(StoreRequest::class)
        ->shouldReceive('validated')
        ->withAnyArgs()
        ->andReturn([
            'event_id'          => $event->id,
            'number_of_tickets' => 50
        ]);

    post(route('api.reservations.store'), [
        'event_id'           => $event->id,
        'number_of_tickets'  => 10,
        'reservation_holder' => fake()->word
    ])
        ->assertBadRequest()
        ->assertJson([
            'status' => true,
            'error'  => trans('response.failed_to_create', [
                'entity' => trans('entities.reservation')
            ]),
            'message' => trans('validation.max_number_of_tickets', [
                'max' => 30
            ])
        ]);
});
