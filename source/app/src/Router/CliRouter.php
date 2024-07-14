<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\RouterContainer;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

final class CliRouter implements RouterInterface
{
    public function __construct(
        private readonly RouterContainer $routerContainer
    ) {
    }

    public function match(ServerRequestInterface $serverRequest): RouterMatch
    {
        $serverParams = $serverRequest->getServerParams();
        $method = strtoupper($serverParams['argv'][1]);
        $path = $serverParams['argv'][2];

        $request = new ServerRequest(
            [],
            [],
            $path,
            $method,
            'php://input',
        );

        $matcher = $this->routerContainer->getMatcher();

        return new RouterMatch(
            $method,
            $path,
            $matcher->match($request),
            $request
        );
    }
}
