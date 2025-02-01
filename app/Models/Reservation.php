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
        parent::boot();

        static::creating(function (self $reservation) {
            if ($reservation->number_of_tickets > $reservation->event->total_availability) {
                $ticketsLabel = str('ticket')
                    ->plural($reservation->event->total_availability)
                    ->toString();

                throw new Exception(
                    $reservation->event->total_availability
                        ? "This event has only {$reservation->event->total_availability} {$ticketsLabel} remaining."
                        : "There are no tickets available for this event."
                );
            }
        });

        static::updated(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });

        static::created(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });

        static::deleted(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });
    }

    private function updateEventAvailability(Event $event)
    {
        $event->update([
            'remaining_availability' => $event->total_availability - $event->reservations()->sum('number_of_tickets')
        ]);
    }
}
