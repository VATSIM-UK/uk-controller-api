# uk-controller-api

![Build and Test](https://github.com/VATSIM-UK/uk-controller-api/workflows/Build%20and%20Test/badge.svg)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=security_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=alert_status)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![codecov](https://codecov.io/gh/VATSIM-UK/uk-controller-api/branch/main/graph/badge.svg)](https://codecov.io/gh/VATSIM-UK/uk-controller-api)

The API backend to the UK Controller Plugin, provides the plugin with information such
as dependency files, squawk allocations and user authentication.

The API is built on [Laravel Framework](https://laravel.com/) and uses [PHPUnit](https://phpunit.de/) for tests.

## System Requirements

- PHP 7.4+
- MySQL 8.0

# Local Deployment

A development environment using `docker-compose` comes bundled with the source, to use it simply run `docker-compose build`
followed by `docker-compose up`.

## Connecting To The Development Database

The development database binds to Port 3306 and can be connected to using the password provided in
the `docker-compose.yml`.

## Running The Websocket Server

The websocket service is automatically started on Port 6001.

## Setup Steps

- Fork the repository to your personal GitHub
- Clone your fork of the repository locally
- Run `composer install`
- Copy `.env.example` to `.env` and fill in the `APP_URL` and database credentials
- Create a new database for the app to use
- Generate an `APP_KEY` for the application by running `php artisan key:generate`
- Run `php artisan migrate` to run database migrations
- Setup Laravel Passport by following the [installation guide](https://laravel.com/docs/5.7/passport#frontend-quickstart)
- Run `php artisan db:seed` to see the database with test data

## Creating A User

- Create a user and generate their API settings file using `php artisan user:create`
- Create a user an api key that can administer other users by running `php artisan user:create-admin`
- Create a user and api key that can administer the data by running `php artisan user:create-data-admin`
- Create a new api key for an existing non-admin user by running `php artisan token:create`
- Delete all api keys for a user by running `php artisan tokens:delete-user`

## Running Tests

Assuming that you have run the migrations and seeded the database, running the tests is as simple as
running the following command:

`./vendor/bin/phpunit`

### Testing with Docker
A separate database is available via the docker deployment on the same network to avoid losing the data contained in the migrations.

The same deployment steps outlined above should be carried out, but append `--env=testing` to run them against the testing database.

Copy the environment file `.env.testing.example` to `.env.testing` to allow these values to be used when running the test suite. 

The test suite will use the `testing` environment via the `APP_ENV` variable set within `phpunit.xml` (the PHPUnit configuration).
In this instance, the phpunit command should be run from within the `web` container so that the docker network is available:

`docker exec -it web /bin/bash`

## Coding Style Checks

To check that the code you have written adheres to the PSR-1 and PSR-2 standards, you can use PHP Codesniffer,
which is a development dependency of this project. You can run it with the following command:

`./vendor/bin/phpcs --file-list=.phpcs --standard=PSR2`

## Linting Dependencies

You can lint the JSON dependencies that plugins will download by running the following command

`php script/lint-dependencies.php`
