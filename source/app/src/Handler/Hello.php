<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use Koriym\HttpConstants\CacheControl;
use Koriym\HttpConstants\ResponseHeader;
use MyVendor\MyPackage\RequestHandler;

final class Hello extends RequestHandler
{
    /** @var array<string, string> */
    public array $headers = [ResponseHeader::CACHE_CONTROL => CacheControl::PUBLIC_ . ',max-age=86400'];

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
