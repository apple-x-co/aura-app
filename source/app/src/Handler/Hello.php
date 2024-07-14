<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;

final class Hello extends AbstractRequestHandler
{
    public function onGet(): self
    {
        return $this;
    }
}
