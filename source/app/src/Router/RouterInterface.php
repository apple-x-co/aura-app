<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $serverRequest): RouterMatch;
}
