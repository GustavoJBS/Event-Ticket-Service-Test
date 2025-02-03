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
        $availableTickets = $this->event()->value('remaining_availability');

        $addedTickets = $this->number_of_tickets - $this->getOriginal('number_of_tickets', 0);

        if ($addedTickets > $availableTickets) {
            throw new Exception(
                $this->getNumberOfTIcketsExceptionMessage($addedTickets, $availableTickets)
            );
        }
    }

    private function getNumberOfTIcketsExceptionMessage(int $addedTickets, int $availableTickets): string
    {
        return match(true) {
            $this->exists && $availableTickets => trans(
                'validation.update_max_number_of_tickets',
                [
                    'addedTickets'     => $addedTickets,
                    'availableTickets' => $availableTickets
                ]
            ),
            !$this->exists && $availableTickets => trans('validation.max_number_of_tickets', [
                'max' => $availableTickets
            ]),
            default => trans('exception.no_ticket_available')
        };
    }
}
