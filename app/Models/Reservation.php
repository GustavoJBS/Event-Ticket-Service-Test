<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function boot(): void
    {
        static::creating(function (self $reservation) {
            if ($reservation->number_of_tickets > $reservation->event->availability) {
                $ticketsLabel = str('ticket')
                    ->plural($reservation->event->availability)
                    ->toString();

                throw new Exception(
                    $reservation->event->availability
                        ? "This event has only {$reservation->event->availability} {$ticketsLabel} remaining."
                        : "There are no tickets available for this event."
                );
            }
        });

        static::created(function (self $reservation) {
            $reservation
                ->event()
                ->decrement(
                    column: 'availability',
                    amount: $reservation->number_of_tickets
                );
        });

        static::deleted(function (self $reservation) {
            $reservation->event()
                ->increment(
                    column: 'availability',
                    amount: $reservation->number_of_tickets
                );
        });
    }

}
