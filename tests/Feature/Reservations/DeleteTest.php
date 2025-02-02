<?php

use App\Models\{Event, Reservation};

use function Pest\Laravel\delete;

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
