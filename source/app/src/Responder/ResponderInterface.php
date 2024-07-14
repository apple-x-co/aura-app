<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Responder;

use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    public function handle(ResponseInterface $response): void;
}
