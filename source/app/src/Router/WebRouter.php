<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;

final class WebRouter implements RouterInterface
{
    public function __construct(
        private readonly RouterContainer $routerContainer,
    ) {
    }

    public function match(ServerRequestInterface $serverRequest): RouterMatch
    {
        $matcher = $this->routerContainer->getMatcher();

        return new RouterMatch(
            $serverRequest->getMethod(),
            $serverRequest->getUri()->getPath(),
            $matcher->match($serverRequest),
            $serverRequest,
        );
    }

    /** @param array<string, string|int> $data */
    public function generate(string $name, array $data = []): string|false
    {
        try {
            return $this->routerContainer->getGenerator()->generate($name, $data);
        } catch (RouteNotFound) {
            return false;
        }
    }
}
