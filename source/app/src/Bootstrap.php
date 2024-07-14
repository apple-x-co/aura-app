<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Aura\Di\Container;
use Koriym\EnvJson\EnvJson;

use MyVendor\MyPackage\Responder\ResponderInterface;
use Psr\Http\Message\ResponseInterface;

use function assert;

final class Bootstrap
{
    public function __invoke(): void
    {
        $appDir = dirname(__DIR__);
        $tmpDir = $appDir . '/var/tmp';

        $this->env($appDir);
        $di = $this->bindDi($appDir, $tmpDir);

        $requestDispatcher = $di->newInstance(RequestDispatcher::class);
        assert($requestDispatcher instanceof RequestDispatcher);
        $response = $requestDispatcher();
        if ($response === null) {
            return;
        }

        assert($response instanceof ResponseInterface);

        $responder = $di->get(ResponderInterface::class);
        assert($responder instanceof ResponderInterface);
        $responder->handle($response);
    }

    private function env(string $appDir): void
    {
        (new EnvJson())->load($appDir);
    }

    private function bindDi(string $appDir, string $tmpDir): Container
    {
        return (new DiBinder())($appDir, $tmpDir);
    }
}
