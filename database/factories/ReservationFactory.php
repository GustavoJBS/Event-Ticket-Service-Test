<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id'           => Event::factory(),
            'reservation_holder' => fake()->name,
            'number_of_tickets'  => fake()->randomNumber(2)
        ];
    }
}
