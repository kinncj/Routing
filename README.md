Routing
=======

[![Build Status](https://travis-ci.org/felipecwb/Routing.svg?branch=master)](https://travis-ci.org/felipecwb/Routing)
[![Latest Stable Version](https://poser.pugx.org/felipecwb/routing/v/stable.svg)](https://packagist.org/packages/felipecwb/routing)
[![License](https://poser.pugx.org/felipecwb/routing/license.svg)](https://packagist.org/packages/felipecwb/routing)


More one simple Routing library for PHP.

*You'll need know about [Regex Patterns](http://php.net/manual/en/pcre.pattern.php).*

## Instalation
[Composer](https://packagist.org/packages/felipecwb/routing):
```json
{
    "felipecwb/routing": "dev-master"
}
```

**Example**:
```php
<?php

use Felipecwb\Routing\Router;
use Felipecwb\Routing\Route;
use Felipecwb\Routing\RouteCollection;
use Felipecwb\Routing\Matcher;
use Felipecwb\Routing\Resolver\CallableResolver;
// Exceptions
use Felipecwb\Routing\Exception\ResolverException;
use Felipecwb\Routing\Exception\RouteNotFoundException;

$router = new Router(
    new Matcher(
        new RouteCollection()
    ),
    new CallableResolver()
);

$router->add(new Route('|/|', function () {
    echo "Hello World!";
});

$router->add(new Route('|/hello/(\w+)|', function ($name) {
    echo "Hello {$name}!";
});

$router->add(new Route('|/article/(\d+)|', function ($id, $extraStr) {
    echo "Article {$id}! ${extraStr}";
});

try {
    $router->dispatch('/'); // output: Hello World!
    // with arguments
    $router->dispatch('/hello/felipecwb'); // output: Hello felipecwb!
    // with extra arguments
    $router->dispatch('/hello/10', ['Extra String!']); // output: Article 10! Extra String!
} catch (RouteNotFoundException $e) {
    echo "Sorry! The target can not be found!";
} catch (ResolverException $e) {
    echo "Sorry! The target can not be executed!";
}

die;
```

[Look in tests for more explanation](tests)

## Contributions

**Feel free to contribute.**

1. Create a issue.
2. Follow the PSR-2 and PSR-4
3. PHPUnit to tests

> License MIT
