<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $maxNumberOfTickets = $this->reservation->number_of_tickets + $this->reservation->event()->value('remaining_availability');

        return [
            'reservation_holder' => ['nullable', 'string', 'min:5', 'max:60'],
            'number_of_tickets'  => [
                'required',
                'numeric',
                'min:1',
                "max:$maxNumberOfTickets"
            ],
        ];
    }

    public function messages(): array
    {
        $availableTickets = $this->reservation->event()->value('remaining_availability');
        return [
            'number_of_tickets.max' => $availableTickets
                ? trans(
                    'validation.update_max_number_of_tickets',
                    [
                        'addedTickets'     => request()->integer('number_of_tickets') - $this->reservation->number_of_tickets,
                        'availableTickets' => $availableTickets
                    ]
                )
                : trans('exception.no_ticket_available')
        ];
    }
}
