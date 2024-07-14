<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Responder;

use Psr\Http\Message\ResponseInterface;

final class CliResponder implements ResponderInterface
{
    public function handle(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}
