# Iamport

[![Packagist](https://img.shields.io/packagist/v/allivcorp/iamport.svg)](https://packagist.org/packages/allivcorp/iamport)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/allivcorp/iamport.svg)](https://packagist.org/packages/allivcorp/iamport)
[![Packagist](https://img.shields.io/packagist/dt/allivcorp/iamport.svg)](https://packagist.org/packages/allivcorp/iamport)
[![Packagist](https://img.shields.io/packagist/l/allivcorp/iamport.svg)](https://packagist.org/packages/allivcorp/iamport)

This package is [I'mport;](http://iamport.kr) REST API module for Laravel 5.

## Installation

Yon can install this package via composer using:

```bash
composer require allivcorp/iamport
```

and then in `.../config/app.php`

```php
    'providers' => [
        // ...
        Alliv\Iamport\IamportServiceProvider::class,
    ]
```

```php
    'aliases' => [
        // ...
        'Iamport' => Alliv\Iamport\Facades\IamportFacade::class,
    ]
```

## Configuration

To publish the config file to `config/iamport.php` run:

```bash
php artisan vendor:publish --provider="Alliv\Iamport\IamportServiceProvider"
```

This will publish a file `iamport.php` in your config directory with the following contents:
```php
return [
    'apiKey' => env('IAMPORT_REST_API_KEY', 'imp_apikey'),
    'apiSecret' => env('IAMPORT_REST_API_SECRET', 'ekKoeW8RyKuT0zgaZsUtXXTLQ4AhPFW3ZGseDA6bkA5lamv9OqDMnxyeB9wqOsuO9W3Mx9YSJ4dTqJ3f')
];
```

## Usage

```php
use Iamport;

// Add subscribe customer (Issue billing key)
Iamport::addSubscribeCustomer('customer_1234', '1234123412341234', '2020-10', '920327', '00');

// Checkout merchant(order)
Iamport::subscribeAgain('customer_1234', 'merchant_1234', 6000, 'Coffee');
```

Furthermore information, please refer to [I'mport API](https://api.iamport.kr/).

## TODO

- Unit test
- Other REST API provided by [I'mport API](https://api.iamport.kr/).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
