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
php artisan migrate --seed
```

**docker**
```shell
./vendor/bin/sail pest --coverage-html coverage
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

