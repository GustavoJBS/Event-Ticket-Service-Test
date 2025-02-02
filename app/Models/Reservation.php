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
            $reservation->validateCurrentRemainingTicket();
        });

        static::created(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });

        static::updating(function (self $reservation) {
            $reservation->validateCurrentRemainingTicket();
        });

        static::updated(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });

        static::deleted(function (self $reservation) {
            $reservation->updateEventAvailability($reservation->event);
        });
    }

    private function updateEventAvailability(Event $event): void
    {
        $newRemainingAvailability = $event->total_availability - $event->reservations()->sum('number_of_tickets');

        $event->update([
            'remaining_availability' => $newRemainingAvailability
        ]);
    }

    private function validateCurrentRemainingTicket(): void
    {
        $remainingAvailability = $this->event()->value('remaining_availability');

        if ($this->number_of_tickets > $remainingAvailability) {
            $ticketsLabel = str(trans('entities.ticket'))
                ->plural($remainingAvailability)
                ->toString();

            throw new Exception(
                trans(
                    $remainingAvailability > 0
                        ? 'exception.tickets_available_not_enough'
                        : 'exception.no_ticket_available',
                    [
                        'total' => $remainingAvailability,
                        'label' => $ticketsLabel
                    ]
                )
            );
        }
    }
}
