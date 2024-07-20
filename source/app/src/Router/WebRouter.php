<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\RouterContainer;
use MyVendor\MyPackage\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

use function file_get_contents;
use function in_array;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function rtrim;

use const JSON_ERROR_NONE;
use const JSON_THROW_ON_ERROR;

final class WebRouter implements RouterInterface
{
    public function __construct(
        private readonly RouterContainer $routerContainer,
    ) {
    }

    public function match(ServerRequestInterface $serverRequest): RouterMatch
    {
        $matcher = $this->routerContainer->getMatcher();

        $isJson = in_array('application/json', $serverRequest->getHeader('content-type'), true);
        if (! $isJson) {
            return new RouterMatch(
                $serverRequest->getMethod(),
                $serverRequest->getUri()->getPath(),
                $matcher->match($serverRequest),
            );
        }

        return new RouterMatch(
            $serverRequest->getMethod(),
            $serverRequest->getUri()->getPath(),
            $matcher->match($serverRequest),
            $this->parseJson($serverRequest),
        );
    }

    private function parseJson(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        $content = json_decode(
            rtrim((string) file_get_contents('php://input')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $serverRequest->withParsedBody($content);
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
