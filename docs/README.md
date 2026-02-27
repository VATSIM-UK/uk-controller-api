# uk-controller-api

![Production Deploy Status](https://github.com/VATSIM-UK/uk-controller-api/workflows/Deploy/badge.svg?branch=main)
![Build and Test](https://github.com/VATSIM-UK/uk-controller-api/workflows/Build%20and%20Test/badge.svg)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=security_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=alert_status)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=VATSIM-UK_uk-controller-api&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=VATSIM-UK_uk-controller-api)
[![codecov](https://codecov.io/gh/VATSIM-UK/uk-controller-api/branch/main/graph/badge.svg)](https://codecov.io/gh/VATSIM-UK/uk-controller-api)
[![semantic-release](https://img.shields.io/badge/%20%20%F0%9F%93%A6%F0%9F%9A%80-semantic--release-e10079.svg)](https://github.com/semantic-release/semantic-release)
[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

The API backend to the UK Controller Plugin, provides the plugin with information such
as dependency files, squawk allocations and user authentication.

The API is built on [Laravel Framework](https://laravel.com/) and uses [PHPUnit](https://phpunit.de/) for tests.

## System Requirements

- PHP 8.1+
- MySQL 8.0

# Local Deployment

A development environment using `docker-compose` comes bundled with the source, to use it simply run `docker-compose build`
followed by `docker-compose up`.

## Connecting To The Development Database

The development database binds to Port 3306 and can be connected to using the password provided in
the `docker-compose.yml`.

## Setup Steps

- Fork the repository to your personal GitHub
- Clone your fork of the repository locally
- Run `composer install`
- Run `npm ci && npm run build` to install and build the frontend assets
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

`php artisan test`

### Testing with Docker

A separate database is available via the docker deployment on the same network to avoid losing the data contained in the migrations.

The same deployment steps outlined above should be carried out, but append `--env=testing` to run them against the testing database.

Copy the environment file `.env.testing.example` to `.env.testing` to allow these values to be used when running the test suite.

The test suite will use the `testing` environment via the `APP_ENV` variable set within `phpunit.xml` (the PHPUnit configuration).
In this instance, the phpunit command should be run from within the `web` container so that the docker network is available:

`docker exec -it web /bin/bash`

## Coding Style

This project uses PSR-12 for its formatting style. You can enforce these standards through most Code Editors, or, alternatively, StyleCI will run on every pull request.

Every pull request is also run through SonarCloud to check for code smells.
