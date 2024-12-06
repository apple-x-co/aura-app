<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Laminas\Diactoros\ServerRequestFactory as LaminasServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function in_array;
use function is_array;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function parse_str;

use const JSON_ERROR_NONE;
use const JSON_THROW_ON_ERROR;

final class ServerRequestFactory
{
    public static function fromGlobals(): ServerRequestInterface
    {
        $serverRequest = LaminasServerRequestFactory::fromGlobals();

        $isFormUrlEncoded = in_array(
            'application/x-www-form-urlencoded',
            $serverRequest->getHeader('content-type'),
            true,
        );
        if ($isFormUrlEncoded) {
            return self::parseFormUrlEncoded($serverRequest);
        }

        $isJson = in_array(
            'application/json',
            $serverRequest->getHeader('content-type'),
            true,
        );
        if (! $isJson) {
            return $serverRequest;
        }

        return self::parseJson($serverRequest);
    }

    private static function parseFormUrlEncoded(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        parse_str((string) $serverRequest->getBody(), $parsedBody);

        return $serverRequest->withParsedBody($parsedBody);
    }

    private static function parseJson(ServerRequestInterface $serverRequest): ServerRequestInterface
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

        assert(is_array($parsedBody));

        return $serverRequest->withParsedBody($parsedBody);
    }
}
