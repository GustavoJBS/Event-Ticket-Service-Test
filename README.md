# BE Project - Event Ticket Service

### Platform Requirements

```text
Laravel Version: 11.31 (default port: 8080)
PHP Version: 8.4
DB Version: MySQL (default port: 3306)
Composer Version: 2.8.5
Node Version: 22.13.1
NPM Version: 11.1.0
Docker
```

### Dependencies

* [Laravel Sail](https://laravel.com/docs/11.x/sail)
* [Pest PHP](https://pestphp.com)
* [Laravel Pint](https://laravel.com/docs/11.x/pint)

### Environment Setup

_Please, enter all the necessary steps to setup the project and start to development._
 
#### 1. Clone the repository

```shell
git clone git@github.com:GustavoJBS/Event-Ticket-Service-Test.git

cd Event-Ticket-Service-Test
```

#### 2. Install the PHP dependencies

```shell
composer install
```

**docker**
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

**sail**

Add the following alias to your .bashrc or .zshrc file to avoid using `./vendor/bin/sail` every time.
```shell
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

#### 3. Copy the .env file

```shell
cp .env.example .env
```

#### 4. If you are using sail (docker) setup your docker application

```shell
./vendor/bin/sail up -d
```

_After copying the .env file, you'll need to change the .env to reflect your environment settings/credentials_

#### 5. Run the migrations and seed the database

```shell
php artisan migrate --seed
```

**docker**
```shell
./vendor/bin/sail artisan migrate --seed
```

### Running Tests with Coverage

```shell
php artisan test --coverage-html coverage
```

**docker**
```shell
./vendor/bin/sail artisan test --coverage-html coverage
```

### Parallel tests with Coverage

```shell
php artisan test --parallel --coverage-html coverage
```

**docker**
```shell
./vendor/bin/sail artisan test --parallel --coverage-html coverage
```

### Verify Code style / test

```shell
composer verify
```

**docker**
```shell
./vendor/bin/sail composer verify
```

## REST API

### Get Events paginated list

#### URL Parameters

```
page: numeric
perPage: numeric, min: 1
sortBy: string, options:id, name, date
sortDirection: string, options:asc, desc
filters:
    name: nullable, string,
    description: nullable, string
    only_available: nullable, boolean (true, false)
    start_date: nullable, date, before:end_date
    end_date: nullable, date, after:start_date
```


#### Curl Command

`GET /api/events`

    curl -i -H 'Accept: application/json' -X GET http://localhost:8080/api/events

#### Response
    HTTP/1.1 200 OK
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:00:08 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": true,
        "message": "Events retrieved successfully.",
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Obon",
                "description": "The festival is based on a legend about a Buddhist monk called Mogallana. The story goes that Mogallana could see into the afterlife and saved his deceased mother from going to hell by giving offerings to Buddhist monks. Having gained redemption for his mother, he danced in celebration, joined by others in a large circle. This dance is known as the Bon Odori dance.",
                "date": "2027-08-13 13:00:00",
                "total_availability": 10,
                "remaining_availability": 10,
                "created_at": "2025-02-03 10:49:59",
                "updated_at": "2025-02-03 10:49:59"
            },
            ...
        ],
        "first_page_url": "http://localhost:8080/api/events?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8080/api/events?page=1",
        "links": [
            {
                "url": null,
                "label": "Previous",
                "active": false
            },
            {
                "url": "http://localhost:8080/api/events?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://localhost:8080/api/events",
        "per_page": 10,
        "prev_page_url": null,
        "to": 5,
        "total": 5
    }



### Get Event with reservations list

#### Curl Command

`GET /api/events/{event}`

    curl -i -H 'Accept: application/json' -X GET http://localhost:8080/api/events/1

#### Response
    HTTP/1.1 200 OK
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:00:08 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": true,
        "message": "Event retrieved successfully.",
        "data": {
            "id": 1,
            "name": "Obon",
            "description": "The festival is based on a legend about a Buddhist monk called Mogallana. The story goes that Mogallana could see into the afterlife and saved his deceased mother from going to hell by giving offerings to Buddhist monks. Having gained redemption for his mother, he danced in celebration, joined by others in a large circle. This dance is known as the Bon Odori dance.",
            "date": "2027-08-13 13:00:00",
            "total_availability": 10,
            "remaining_availability": 10,
            "created_at": "2025-02-03 10:49:59",
            "updated_at": "2025-02-03 10:49:59",
            "reservations": []
        }
    }

### Create a Event Reservation

#### Parameters

```
event_id: required, exists: table=events, column=id
reservation_holder: required, string, min:5, max: 60
number_of_tickets: required, numeric, min:1, max:{available event tickets}
```

#### Curl Command

`POST /api/reservations`

    curl -v -i -H 'Accept: application/json' -d 'event_id=1&reservation_holder=Reservation%20Holder&number_of_tickets=5' -X POST http://localhost:8080/api/reservations
    
#### Response
    HTTP/1.1 201 Created
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:16:13 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": true,
        "message": "Reservation created successfully.",
        "data": {
            "event_id": "1",
            "reservation_holder": "Reservation Holder",
            "number_of_tickets": "5",
            "updated_at": "2025-02-03 11:16:13",
            "created_at": "2025-02-03 11:16:13",
            "id": 1,
            "event": {
                "id": 1,
                "name": "Obon",
                "description": "The festival is based on a legend about a Buddhist monk called Mogallana. The story goes that Mogallana could see into the afterlife and saved his deceased mother from going to hell by giving offerings to Buddhist monks. Having gained redemption for his mother, he danced in celebration, joined by others in a large circle. This dance is known as the Bon Odori dance.",
                "date": "2027-08-13 13:00:00",
                "total_availability": 10,
                "remaining_availability": 5,
                "created_at": "2025-02-03 11:16:04",
                "updated_at": "2025-02-03 11:16:13"
            }
        }
    }

### Update a Event Reservation

#### Parameters

```
reservation_holder: nullable, string, min:5, max: 60
number_of_tickets: required, numeric, min:1, max:{available event tickets}
```

#### Curl Command

`PUT /api/reservations/{reservation}`

    curl -i -H 'Accept: application/json' -d 'reservation_holder=Reservation%20Holder2&number_of_tickets=6' -X PUT http://localhost:8080/api/reservations/1

#### Response
    HTTP/1.1 200 OK
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:19:32 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": true,
        "message": "Reservation updated successfully.",
        "data": {
            "id": 1,
            "event_id": 1,
            "reservation_holder": "Reservation Holder2",
            "number_of_tickets": "6",
            "created_at": "2025-02-03 11:18:26",
            "updated_at": "2025-02-03 11:19:32",
            "event": {
                "id": 1,
                "name": "Obon",
                "description": "The festival is based on a legend about a Buddhist monk called Mogallana. The story goes that Mogallana could see into the afterlife and saved his deceased mother from going to hell by giving offerings to Buddhist monks. Having gained redemption for his mother, he danced in celebration, joined by others in a large circle. This dance is known as the Bon Odori dance.",
                "date": "2027-08-13 13:00:00",
                "total_availability": 10,
                "remaining_availability": 4,
                "created_at": "2025-02-03 11:18:20",
                "updated_at": "2025-02-03 11:19:32"
            }
        }
    }

### Delete a Event Reservation

#### Curl Command

`DELETE /api/reservations/{reservation}`

    curl -i -H 'Accept: application/json' -X DELETE http://localhost:8080/api/reservations/1

#### Response
    HTTP/1.1 200 OK
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:25:04 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": true,
        "message": "Reservation cancelled successfully."
    }

### Model Not Found Response Example

#### Curl Command

`DELETE /api/reservations/{reservation}`

    curl -i -H 'Accept: application/json' -X DELETE http://localhost:8080/api/reservations/123456

#### Response
    HTTP/1.1 404 Not Found
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:29:23 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": false,
        "message": "The Reservation provided was not found."
    }

### Endpoint not found Response Example

#### Curl Command

`GET /api/random-endpoint-xyz`

    curl -i -H 'Accept: application/json' -X GET http://localhost:8080/api/random-endpoint-xyz

#### Response
    HTTP/1.1 404 Not Found
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:30:38 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": false,
        "message": "The requested URL was not found on this server."
    }

### Validation Attributes Response Example

#### Curl Command

`POST /api/reservations`

    curl -i -H 'Accept: application/json' -X POST http://localhost:8080/api/reservations

#### Response
    HTTP/1.1 422 Unprocessable Content
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:31:45 GMT
    Content-Type: application/json
    X-RateLimit-Limit: 60
    X-RateLimit-Remaining: 59
    Access-Control-Allow-Origin: *

    {
        "status": false,
        "message": "The request parameters are invalid.",
        "errors": {
            "event_id": [
                "The event id field is required."
            ],
            "reservation_holder": [
                "The reservation holder field is required."
            ],
            "number_of_tickets": [
                "The number of tickets field is required."
            ]
        }
    }

### Method Not Allowed Response Example

#### Curl Command

`POST /api/events`

    curl -i -H 'Accept: application/json' -X POST http://localhost:8080/api/events

#### Response
    HTTP/1.0 405 Method Not Allowed
    Host: localhost:8080
    Connection: close
    X-Powered-By: PHP/8.4.3
    Cache-Control: no-cache, private
    Date: Mon, 03 Feb 2025 11:33:17 GMT
    Content-Type: application/json
    Access-Control-Allow-Origin: *

    {
        "status": false,
        "message": "The POST method is not supported for route api/events. Supported methods: GET, HEAD."
    }
