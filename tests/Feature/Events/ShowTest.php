<?php

use App\Models\{Event, Reservation};

use function Pest\Laravel\get;

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
        ->assertJsonCount(5, 'data.reservations')
        ->assertJson([
            'status'  => true,
            'message' => trans('response.retrieved', [
                'entity' => trans('entities.event')
            ]),
            'data' => $event->toArray()
        ]);
});
