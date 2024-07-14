<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;

final class HtmlRenderer implements RendererInterface
{
    public function render(AbstractRequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        if ($requestHandler->string === null) {
            // TODO: Use qiq
            $requestHandler->string = '<!DOCTYPE html><html lang="ja"><body><h1>HELLO WORLD!!</h1></body></html>';
        }

        return $requestHandler->string;
    }
}
