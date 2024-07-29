<?php

declare(strict_types=1);

use Aura\Router\Map;
use MyVendor\MyPackage\Handler;
use MyVendor\MyPackage\Handler\Admin as AdminHandler;

/* @var Map $map */

if (empty($adminPrefix)) {
    $adminPrefix = 'admin';
}

$map->attach(null, null, function (Map $map) {
    $map->accepts(['application/json', 'text/html']);

    $map->get('hello', '/hello', Handler\Hello::class)
        ->extras(['a' => 'b']);
});

$map->attach('/admin', '/' . $adminPrefix, function (Map $map) {
    $auth = ['admin' => true];
    $map->auth($auth);

    $map->get('/login', '/login', AdminHandler\Login::class)
        ->auth([]);

    $map->post('/_login', '/login', AdminHandler\Login::class)
        ->auth(['login' => true]);

    $map->post('/logout', '/logout', AdminHandler\Logout::class)
        ->auth(array_merge($auth, ['logout' => true]));

    $map->get('/index', '/index', AdminHandler\Index::class);

    $map->get('/hello', '/hello', AdminHandler\Hello::class);
});
