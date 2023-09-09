# selective/basepath

A URL base path detector for Slim 4.

[![Latest Version on Packagist](https://img.shields.io/github/release/selective-php/basepath.svg)](https://packagist.org/packages/selective/basepath)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://github.com/selective-php/basepath/workflows/build/badge.svg)](https://github.com/selective-php/basepath/actions)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/selective-php/basepath.svg)](https://scrutinizer-ci.com/g/selective-php/basepath/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/selective-php/basepath.svg)](https://scrutinizer-ci.com/g/selective-php/basepath/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/selective/basepath.svg)](https://packagist.org/packages/selective/basepath/stats)


## Features

* Support for Apache and the PHP built-in webserver
* Tested
* No dependencies
* Very fast

### Supported servers

* Apache webserver with mod_rewrite and .htaccess
* PHP build-in webserver 

## Requirements

* PHP 7.2+ or 8.1+

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

### Apache usage

* Start the apache webserver
* Open your website, e.g. `http://localhost` or `http://localhost/{my-sub-directory}` and you should see the message `Hello, World!`.

### PHP built-in webserver usage

* Open the console and change into the project `public/` directory. Then run:

```
php -S localhost:8000
```

If you don't start the webserver from the project `public/` directory, you have start it with a specific document root directory:

```
php -S localhost:8000 -t public
```

* Open `http://localhost:8000` and you should see the message `Hello, World!`.

### Good URLs

The `public/` directory is only the `DocumentRoot` of your webserver, 
but it's never part of your base path and the official url.

<span style="color:green">Good URLs:</span>

* `https://www.example.com`
* `https://www.example.com/users`
* `https://www.example.com/my-app`
* `https://www.example.com/my-app/users`

<span style="color:red">Bad URLs:</span>
 
* `https://www.example.com/public`
* `https://www.example.com/public/users`
* `https://www.example.com/my-app/public`
* `https://www.example.com/my-app/public/users`

### Retrieving the base path

```php
$basePath = \Slim\Routing\RouteContext::fromRequest($request)->getBasePath();
```

### Creating a relative url with the base path

```php
$routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
$url = $routeParser->urlFor('root');
```

### Rendering the base path into a Twig layout template

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

## Support

* Issues: <https://github.com/selective-php/basepath/issues>
* Here you can [donate](https://odan.github.io/donate.html) for this project.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

