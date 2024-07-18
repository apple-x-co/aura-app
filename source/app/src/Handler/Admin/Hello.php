<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\RequestHandler;

final class Hello extends RequestHandler
{
    public function onGet(): self
    {
        return $this;
    }
}
