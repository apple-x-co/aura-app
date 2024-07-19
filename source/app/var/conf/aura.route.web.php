<?php

declare(strict_types=1);

use Aura\Router\Map;
use MyVendor\MyPackage\Handler;

/* @var Map $map */

$map->attach(null, null, function (Map $map) {
    $map->accepts(['application/json', 'text/html']);

    $map->get('hello', '/hello', Handler\Hello::class)
        ->extras(['a' => 'b']);
});

$map->attach('admin:', '/admin', function (Map $map) {
    $map->auth(['admin' => true]);

    $map->get('login', '/login', Handler\Admin\Login::class)
        ->auth(['admin' => false]);

    $map->get('hello', '/hello', Handler\Admin\Hello::class);
});
