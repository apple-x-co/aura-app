<?php

declare(strict_types=1);

/* @var \Aura\Router\Map $map */

use MyVendor\MyPackage\Handler;

$map->get(
    '/hello',
    '/hello',
    Handler\Hello::class,
);
