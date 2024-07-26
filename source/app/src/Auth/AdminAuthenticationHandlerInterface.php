<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use MyVendor\MyPackage\Router\RouterMatch;
use Psr\Http\Message\ResponseInterface;

interface AdminAuthenticationHandlerInterface
{
    public function __invoke(RouterMatch $routerMatch): ResponseInterface|null;
}
