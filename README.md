# selective/basepath

A URL base path detector for Slim 3 and 4.

[![Latest Version on Packagist](https://img.shields.io/github/release/selective-php/basepath.svg?style=flat-square)](https://packagist.org/packages/selective/basepath)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/selective-php/basepath/master.svg?style=flat-square)](https://travis-ci.org/selective-php/basepath)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/selective-php/basepath.svg?style=flat-square)](https://scrutinizer-ci.com/g/selective-php/basepath/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/selective-php/basepath.svg?style=flat-square)](https://scrutinizer-ci.com/g/selective-php/basepath/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/selective/basepath.svg?style=flat-square)](https://packagist.org/packages/selective/basepath/stats)


## Features

* Support for multiple servers
* Tested
* No dependencies
* Very fast

### Supported servers

* Apache webserver
* PHP build-in webserver

## Requirements

* PHP 7.2+

## Installation

```
composer require selective/basepath
```

For **Apache** we have to "redirect" the web traffic to the front controller
in `public/index.php`. 

Create a file: `public/.htaccess` with this content:

```htaccess
# Redirect to front controller
RewriteEngine On
# RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

We also need a rule to "redirect" the sub-directories to 
the front-controller in `public/index.php`. 

Create a `.htaccess` file above the `public/` directory with this content:

```htaccess
RewriteEngine on
RewriteRule ^$ public/ [L]
RewriteRule (.*) public/$1 [L]
```

## Usage

### Detect the base path

```php
use Selective\BasePath\BasePathDetector;

$basePathDetector = new BasePathDetector($_SERVER);

echo $basePathDetector->getBasePath();
```

### Slim 4 integration

Example: `public/index.php`

```php
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$basePath = (new BasePathDetector($_SERVER))->getBasePath();

// Add middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/', static function (Request $request, Response $response) {
    $response->getBody()->write('Hello, World!');
    return $response;
})->setName('root');

// Run app
$app->run();
```

Creating a url with a base path using the RouteParser:

```php
$routeParser = $app->getRouteCollector()->getRouteParser();
echo $routeParser->urlFor('root');
```

Passing a global base url into all Twig templates:

```php
$twig->addGlobal('base_url', $routeParser->urlFor('root'));
```

## License

* MIT
