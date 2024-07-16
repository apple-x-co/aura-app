<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Renderer;

use MyVendor\MyPackage\AbstractRequestHandler;

interface RendererInterface
{
    public function render(AbstractRequestHandler $requestHandler): string;
}
