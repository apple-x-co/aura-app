<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;

interface RendererInterface
{
    public function render(AbstractRequestHandler $requestHandler): string;
}
