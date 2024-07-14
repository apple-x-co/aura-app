<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;

final class TextRenderer implements RendererInterface
{
    public function render(AbstractRequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'plain/text';
        }

        return 'Hello World!';
    }
}
