<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Responder;

use Psr\Http\Message\ResponseInterface;

use const PHP_EOL;

final class CliResponder implements ResponderInterface
{
    public function handle(ResponseInterface $response): void
    {
        echo 'code: ' . $response->getStatusCode() . PHP_EOL;

        echo PHP_EOL; // NOTE: Empty line

        echo $response->getBody() . PHP_EOL;
    }
}
