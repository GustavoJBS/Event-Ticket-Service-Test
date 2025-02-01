<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $maxNumberOfTickets = $this->reservation->number_of_tickets + $this->reservation->event()->value('remaining_availability');

        return [
            'number_of_tickets' => [
                'required',
                'numeric',
                'min:1',
                "max:$maxNumberOfTickets"
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'number_of_tickets.max' => trans('validation.max_number_of_tickets')
        ];
    }
}
