<?php

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
