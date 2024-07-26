<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use MyVendor\MyPackage\RequestHandler;

final class Hello extends RequestHandler
{
    public function onGet(): self
    {
        $this->body['Hello'] = 'world';

        return $this;
    }

    public function __toString(): string
    {
        return 'Hello World!?';
    }
}
