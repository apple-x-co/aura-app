<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use MyVendor\MyPackage\AbstractRequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Stringable;

final class Hello extends AbstractRequestHandler implements Stringable
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
