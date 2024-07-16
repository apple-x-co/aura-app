<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

final class RouterMatch
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly Route|false $route,
        public readonly ServerRequestInterface|null $serverRequest = null,
    ) {
    }
}
