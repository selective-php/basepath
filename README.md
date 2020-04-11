# selective/basepath

A URL base path detector for Slim 4.

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

* PHP 7.1+

## Installation

```
composer require selective/basepath
```

The recommended **directory structure**: 

* `public/`      Web server files, the [DocumentRoot](https://httpd.apache.org/docs/2.4/de/mod/core.html#documentroot)
  * `.htaccess`   Apache redirect rules for the front controller
  * `index.php`   The front controller
* `.htaccess`    Internal redirect to the public/ directory

The following steps are necessary for your Slim 4 application:

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

Create a second `.htaccess` file above the `public/` directory with this content:

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

$basePath = $basePathDetector->getBasePath();
```

### Slim 4 integration

Add the `BasePathMiddleware` after `addRoutingMiddleware()` to set the basePath before 
the routing is started. 

Example: `public/index.php`

```php
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Add Slim routing middleware
$app->addRoutingMiddleware();

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Hello, World!');
    return $response;
})->setName('root');

// Run app
$app->run();
```

#### Retrieving the base path

```php
$basePath = \Slim\Routing\RouteContext::fromRequest($request)->getBasePath(),
```

#### Creating a relative url with the base path

```php
$routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
$url = $routeParser->urlFor('root');
```

#### Rendering the base path into a Twig layout template

This example requires [slim/twig-view](https://github.com/slimphp/Twig-View)

```twig
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <base href="{{ base_path() }}/"/>
</head>
<body>
{% block content %}{% endblock %}
</body>
</html>
```

## License

* MIT
