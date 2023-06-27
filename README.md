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

Add the route middleware in kernel file 

'validateuser' =>\App\Http\Middleware\ValidateUserMiddleware::class,

## Publish the Assets

Run the following command to publish the files in package 

php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force

php artisan jwt:secret

php artisan vendor:publish --force --provider="snosborn\laraveljwtauth\UserServiceProvider"