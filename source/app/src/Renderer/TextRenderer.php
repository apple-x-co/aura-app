<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler;

final class TextRenderer implements RendererInterface
{
    public function render(RequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'plain/text';
        }

        if ($requestHandler->string === null) {
            $requestHandler->string = (string) $requestHandler;
        }

        return $requestHandler->string;
    }
}
