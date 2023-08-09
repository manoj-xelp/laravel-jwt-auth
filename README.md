# Laravel User Auth package

## Installation

Install the package by the following command,

    composer require snosborn/laraveljwtauth:VERSION

## Add Provider

Add the provider to your config/app.php into provider section if using lower version
of laravel,

    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    snosborn\laraveljwtauth\UserServiceProvider::class,

## Add Facade

Add the Facade to your config/app.php into aliases section,

    'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
    'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,

## Add Guard in Auth file

Add the guard for api in config\auth.php file

 'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

  'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
## Add validator

Add the route middleware in middleware aliase section on kernel file 

    'validateuser' =>\App\Http\Middleware\ValidateUserMiddleware::class,

## Publish the Assets

Run the following command to publish the files in package 

    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force

    php artisan jwt:secret

    php artisan vendor:publish --force --provider="snosborn\laraveljwtauth\UserServiceProvider"

## API DOC OPEN API 3.0

Run the following command to publish the package files

    php artisan vendor:publish --tag=request-docs-config

    php artisan route:cache

# Optional publish assets
Currently after doing this the request doc url showing not found

    php artisan vendor:publish --tag=request-docs-assets

# Optional middleware

(optional) Add the following middleware to your API, so that the SQL logs and model events are captured.

    app/Http/Kernel.php

        'api' => [
            ...
            \Rakutentech\LaravelRequestDocs\LaravelRequestDocsMiddleware::class,
            ... and so on

Usage: 
    View in the browser on /request-docs/

# Environment Variable changes

Add below mentioned variables to .env 

    SERVER_URL=BACKEND_SERVER_URL

# Composer file changes

Add below snippet to autoload section in composer.json file.

    "files": [
            "app/Helpers/Helper.php"
        ]

Run composer install after adding 

# Countries,States, Cities data seeder

Run the seeder for insert data into respective tables after migrations

    php artisan db:seed --class=CountryStateCitySeeder

Note: This seeder will take upto 30 minutes due to large data sets.
