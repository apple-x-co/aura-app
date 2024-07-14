<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;

use function json_encode;

final class JsonRenderer implements RendererInterface
{
    public function render(AbstractRequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'application/json';
        }

        return json_encode([
            'success' => true,
        ], JSON_THROW_ON_ERROR);
    }
}
