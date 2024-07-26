<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\RequestHandler;

interface RendererInterface
{
    public function render(RequestHandler $requestHandler): string;
}
