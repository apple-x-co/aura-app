<?php

declare(strict_types=1);

use Aura\Router\Map;
use MyVendor\MyPackage\Handler;

/* @var Map $map */

$map->attach('cli:', null, function (Map $map) {
    $map->get('hello', '/hello', Handler\Hello::class,)
        ->extras(['a' => 'b']);
});
