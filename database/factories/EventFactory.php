<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => fake()->name,
            'description'        => fake()->sentence(),
            'date'               => fake()->dateTime(),
            'total_availability' => fake()->randomNumber(3)
        ];
    }
}
