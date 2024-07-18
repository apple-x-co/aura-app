<?php

declare(strict_types=1);

use Aura\Router\Map;
use MyVendor\MyPackage\Handler;

/* @var Map $map */

$map->get('hello', '/hello', Handler\Hello::class)
    ->extras(['a' => 'b']);

$map->attach('admin', '/admin', function (Map $map) {
    $map->auth(['admin' => true]);

    $map->get('hello', '/hello', Handler\Admin\Hello::class);
});
