<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Responder;

use Psr\Http\Message\ResponseInterface;

use function header;
use function http_response_code;
use function sprintf;

use const PHP_EOL;

final class WebResponder implements ResponderInterface
{
    public function handle(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        echo $response->getBody() . PHP_EOL;
    }
}
