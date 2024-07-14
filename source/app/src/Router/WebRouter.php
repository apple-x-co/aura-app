<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\RouterContainer;
use MyVendor\MyPackage\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WebRouter implements RouterInterface
{
    public function __construct(
        private readonly RouterContainer $routerContainer
    ) {
    }

    public function match(ServerRequestInterface $serverRequest): RouterMatch
    {
        $matcher = $this->routerContainer->getMatcher();

        return new RouterMatch(
            $serverRequest->getMethod(),
            $serverRequest->getUri()->getPath(),
            $matcher->match($serverRequest)
        );
    }
}