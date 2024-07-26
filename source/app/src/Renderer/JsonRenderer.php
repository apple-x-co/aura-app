<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler;

use function json_encode;

use const JSON_THROW_ON_ERROR;

final class JsonRenderer implements RendererInterface
{
    public function render(RequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'application/json';
        }

        if ($requestHandler->string === null && $requestHandler->body !== null) {
            $requestHandler->string = json_encode($requestHandler->body, JSON_THROW_ON_ERROR);
        }

        return $requestHandler->string ?? '{}';
    }
}
