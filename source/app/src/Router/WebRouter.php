<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function parse_str;

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

        $isFormUrlEncoded = in_array(
            'application/x-www-form-urlencoded',
            $serverRequest->getHeader('content-type'),
            true,
        );
        if ($isFormUrlEncoded) {
            return new RouterMatch(
                $serverRequest->getMethod(),
                $serverRequest->getUri()->getPath(),
                $matcher->match($serverRequest),
                $this->parseFormUrlEncoded($serverRequest),
            );
        }

        $isJson = in_array(
            'application/json',
            $serverRequest->getHeader('content-type'),
            true,
        );
        if (! $isJson) {
            return new RouterMatch(
                $serverRequest->getMethod(),
                $serverRequest->getUri()->getPath(),
                $matcher->match($serverRequest),
                $serverRequest,
            );
        }

        return new RouterMatch(
            $serverRequest->getMethod(),
            $serverRequest->getUri()->getPath(),
            $matcher->match($serverRequest),
            $this->parseJson($serverRequest),
        );
    }

    private function parseFormUrlEncoded(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        parse_str((string) $serverRequest->getBody(), $parsedBody);

        return $serverRequest->withParsedBody($parsedBody);
    }

    private function parseJson(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        $parsedBody = json_decode(
            (string) $serverRequest->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            throw new InvalidRequestException(json_last_error_msg());
        }

        return $serverRequest->withParsedBody($parsedBody);
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
