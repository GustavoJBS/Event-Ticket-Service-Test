<?php

dataset('events-filter-validations', [
    'query-pagination' => [
        'data' => [
            'page'          => fake()->word,
            'perPage'       => fake()->word,
            'sortBy'        => fake()->name,
            'sortDirection' => fake()->name,
        ],
        'errors' => [
            'page'          => ['The page field must be a number.'],
            'perPage'       => ['The per page field must be a number.'],
            'sortBy'        => ['The selected sort by is invalid.'],
            'sortDirection' => ['The selected sort direction is invalid.'],
        ]
    ],
    'per-page-min-value' => [
        'data' => [
            'perPage' => -1,
        ],
        'errors' => [
            'perPage' => ['The per page field must be at least 1.'],
        ]
    ],
    'filter-valid-types' => [
        'data' => [
            'filters' => [
                'name'           => [fake()->word],
                'description'    => [fake()->word],
                'only_available' => fake()->word,
                'start_date'     => fake()->word,
                'end_date'       => fake()->word,
            ],
        ],
        'errors' => [
            'filters.name'           => ['The name field must be a string.'],
            'filters.description'    => ['The description field must be a string.'],
            'filters.only_available' => ['The only available field must be true or false.'],
            'filters.start_date'     => ['The start date field must be a valid date.'],
            'filters.end_date'       => ['The end date field must be a valid date.'],
        ]
    ],
    'filter-date-validation' => [
        'data' => [
            'perPage' => -1,
            'filters' => [
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date'   => now()->format('Y-m-d'),
            ],
        ],
        'errors' => [
            'filters.start_date' => ['The start date field must be a date before end date.'],
            'filters.end_date'   => ['The end date field must be a date after start date.'],
        ]
    ]
]);

dataset('events-filters', [
    'only-available' => [
        'filters' => [
            'name'           => '',
            'only_available' => true
        ],
    ],
    'name' => [
        'filters' => [
            'name' => fake()->name
        ],
    ],
    'description' => [
        'filters' => [
            'description' => fake()->sentence
        ],
    ],
    'date' => [
        'filters' => [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date'   => now()->addDay()->format('Y-m-d')
        ],
    ]
]);

dataset(
    'reservation-store-validations',
    [
        'empty-data' => [
            'data'   => [],
            'errors' => [
                'event_id'          => ['The event id field is required.'],
                'number_of_tickets' => ['The number of tickets field is required.']
            ]
        ],
        'invalid-data-parameters' => [
            'data' => [
                'event_id'          => PHP_INT_MAX,
                'number_of_tickets' => PHP_INT_MAX
            ],
            'errors' => [
                'event_id'          => ['The selected event id is invalid.'],
                'number_of_tickets' => ["Not enough tickets available. There are only 0 tickets available for this event. Please reduce the number of tickets you've selected."]
            ]
        ],
        'number-of-tickets-bigger-than-available' => [
            'data' => [
                'number_of_tickets' => PHP_INT_MAX
            ],
            'errors' => [
                'number_of_tickets' => [
                    "Not enough tickets available. There are only 100 tickets available for this event. Please reduce the number of tickets you've selected."
                ]
            ],
            'createEvent' => true,
        ],
        'reservation-holder' => [
            'data' => [
                'reservation_holder' => ''
            ],
            'errors' => [
                'reservation_holder' => [
                    "The reservation holder field is required."
                ]
            ],
        ],
        'reservation-holder' => [
            'data' => [
                'reservation_holder' => [fake()->word]
            ],
            'errors' => [
                'reservation_holder' => [
                    "The reservation holder field must be a string."
                ]
            ],
        ],
        'reservation-holder-min' => [
            'data' => [
                'reservation_holder' => fake()->randomLetter
            ],
            'errors' => [
                'reservation_holder' => [
                    "The reservation holder field must be at least 5 characters."
                ]
            ],
        ],
        'reservation-holder-max' => [
            'data' => [
                'reservation_holder' => fake()->sentence(200)
            ],
            'errors' => [
                'reservation_holder' => [
                    "The reservation holder field must not be greater than 60 characters."
                ]
            ],
        ],
    ]
);

dataset('reservation-update-validations', [
    'number-of-tickets-required' => [
        'data'   => [],
        'errors' => [
            'number_of_tickets' => ['The number of tickets field is required.']
        ]
    ],
    'number-of-tickets-min' => [
        'data'   => ['number_of_tickets' => -1],
        'errors' => [
            'number_of_tickets' => ['The number of tickets field must be at least 1.']
        ]
    ],
    'number-of-tickets-max-with-available' => [
        'data'   => ['number_of_tickets' => 200],
        'errors' => [
            'number_of_tickets' => [
                "You added 180 tickets to this reservation, there are only 80 tickets available for this event. Please reduce the number of tickets you've selected."
            ]
        ]
    ],
    'number-of-tickets-max-without-available' => [
        'data'            => ['number_of_tickets' => 200],
        'numberOfTickets' => 100,
        'errors'          => [
            'number_of_tickets' => [
                "There are no tickets available for this event."
            ]
        ]
    ],
    'reservation-holder-min' => [
        'data' => [
            'reservation_holder' => fake()->randomLetter
        ],
        'errors' => [
            'reservation_holder' => [
                "The reservation holder field must be at least 5 characters."
            ]
        ],
    ],
    'reservation-holder-max' => [
        'data' => [
            'reservation_holder' => fake()->sentence(200)
        ],
        'errors' => [
            'reservation_holder' => [
                "The reservation holder field must not be greater than 60 characters."
            ]
        ],
    ]
]);
