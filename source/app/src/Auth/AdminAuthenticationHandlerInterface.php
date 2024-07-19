<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;

interface AdminAuthenticationHandlerInterface
{
    public function __invoke(Route $route): ResponseInterface|null;
}
