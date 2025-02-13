<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $serverRequest): RouterMatch;

    /** @param array<string, string|int> $data */
    public function generate(string $name, array $data = []): string|false;
}
