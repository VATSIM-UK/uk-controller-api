# uk-controller-api

![Laravel Tests](https://github.com/VATSIM-UK/uk-controller-api/workflows/Laravel%20Tests/badge.svg)

The API backend to the UK Controller Plugin, provides the plugin with information such
as dependency files, squawk allocations and user authentication.

The API is built on [Laravel Framework](https://laravel.com/) and uses [PHPUnit](https://phpunit.de/) for tests.

## System Requirements

- PHP 7.3+
- MySQL 8.0

# Local Deployment

It is recommended that you use the [laravel/homestead](https://laravel.com/docs/homestead) Vagrant machine when developing
the API. For this you will need Vagrant installed [More Information](https://www.vagrantup.com/downloads.html).

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
- Create a new api key for an existing non-admin user by running `php artisan token:create`
- Delete all api keys for a user by running `php artisan tokens:delete-user`

## Running Tests

Assuming that you have run the migrations and seeded the database, running the tests is as simple as
running the following command:

`./vendor/bin/phpunit`

## Coding Style Checks

To check that the code you have written adheres to the PSR-1 and PSR-2 standards, you can use PHP Codesniffer,
which is a development dependency of this project. You can run it with the following command:

`./vendor/bin/phpcs --file-list=.phpcs --standard=PSR2`

## Linting Dependencies

You can lint the JSON dependencies that plugins will download by running the following command

`php script/lint-dependencies.php`
