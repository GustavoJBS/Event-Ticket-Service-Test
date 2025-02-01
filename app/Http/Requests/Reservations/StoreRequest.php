<?php

namespace App\Http\Requests\Reservations;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        $maxNumberOfTickets = Event::query()
            ->where(
                'id',
                request()->integer('event_id')
            )->value('remaining_availability', 0);

        return [
            'event_id'          => ['required', Rule::exists(Event::class, 'id')],
            'number_of_tickets' => [
                'required',
                'numeric',
                'min:1',
                "max:$maxNumberOfTickets"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'number_of_tickets.max' => trans('validation.max_number_of_tickets')
        ];
    }
}
