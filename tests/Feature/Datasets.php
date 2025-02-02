<?php

dataset('events-filter-validations', [
    [
        'data' => [
            'page'          => fake()->word,
            'perPage'       => fake()->word,
            'sortBy'        => fake()->name,
            'sortDirection' => fake()->name,
            'filters'       => [
                'name'           => [fake()->word],
                'description'    => [fake()->word],
                'only_available' => fake()->word,
                'start_date'     => fake()->word,
                'end_date'       => fake()->word,
            ],
        ],
        'errors' => [
            'page'                   => ['The page field must be a number.'],
            'perPage'                => ['The per page field must be a number.'],
            'sortBy'                 => ['The selected sort by is invalid.'],
            'sortDirection'          => ['The selected sort direction is invalid.'],
            'filters.name'           => ['The name field must be a string.'],
            'filters.description'    => ['The description field must be a string.'],
            'filters.only_available' => ['The only available field must be true or false.'],
            'filters.start_date'     => ['The start date field must be a valid date.'],
            'filters.end_date'       => ['The end date field must be a valid date.'],
        ]
    ],
    [
        'data' => [
            'perPage' => -1,
            'filters' => [
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date'   => now()->format('Y-m-d'),
            ],
        ],
        'errors' => [
            'perPage'            => ['The per page field must be at least 1.'],
            'filters.start_date' => ['The start date field must be a date before end date.'],
            'filters.end_date'   => ['The end date field must be a date after start date.'],
        ]
    ]
]);

dataset('events-filters', [
    [
        'filters' => [
            'name'           => '',
            'only_available' => true
        ],
    ],
    [
        'filters' => [
            'name' => fake()->name
        ],
    ],
    [
        'filters' => [
            'description' => fake()->sentence
        ],
    ],
    [
        'filters' => [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date'   => now()->addDay()->format('Y-m-d')
        ],
    ]
]);
