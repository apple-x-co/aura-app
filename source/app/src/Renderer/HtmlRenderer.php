<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler;
use MyVendor\MyPackage\TemplateEngine\QiqRenderer;

final class HtmlRenderer implements RendererInterface
{
    public function __construct(
        private readonly QiqRenderer $qiqRenderer,
    ) {
    }

    public function render(RequestHandler $requestHandler): string
    {
        if (! isset($requestHandler->headers['Content-Type'])) {
            $requestHandler->headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        if ($requestHandler->string === null) {
            $requestHandler->string = $this->qiqRenderer->render($requestHandler);
        }

        return $requestHandler->string;
    }
}
