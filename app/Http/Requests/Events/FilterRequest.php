<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page'                   => ['numeric'],
            'perPage'                => ['numeric', 'min:1'],
            'sortBy'                 => ['string', 'in:id,name,date'],
            'sortDirection'          => ['string', 'in:asc,desc'],
            'filters'                => ['array'],
            'filters.name'           => ['nullable', 'string'],
            'filters.description'    => ['nullable', 'string'],
            'filters.only_available' => ['nullable', 'boolean'],
            'filters.start_date'     => ['nullable', 'date', 'before:filters.end_date'],
            'filters.end_date'       => ['nullable', 'date', 'after:filters.start_date']
        ];
    }

    public function attributes(): array
    {
        return [
            'filters.name'           => 'name',
            'filters.description'    => 'description',
            'filters.only_available' => 'only available',
            'filters.start_date'     => 'start date',
            'filters.end_date'       => 'end date',
        ];
    }
}
