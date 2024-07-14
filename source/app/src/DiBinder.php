<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Aura\Di\Container;
use Aura\Di\ContainerBuilder;
use Aura\Router\RouterContainer;
use Koriym\QueryLocator\QueryLocator;
use MyVendor\MyPackage\Responder\CliResponder;
use MyVendor\MyPackage\Responder\ResponderInterface;
use MyVendor\MyPackage\Responder\WebResponder;
use MyVendor\MyPackage\Router\CliRouter;
use MyVendor\MyPackage\Router\WebRouter;

use function file_exists;

use const PHP_SAPI;

final class DiBinder
{
    public function __invoke(string $appDir, string $tmpDir): Container
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance();

        $this->appMeta($di, $appDir, $tmpDir);
        $this->queryLocator($di, $appDir);
        $this->responder($di);
        $this->requestDispatcher($di);
        $this->router($di, $appDir);

        return $di;
    }

    private function appMeta(Container $di, string $appDir, string $tmpDir): void
    {
        $di->params[AppMeta::class]['appDir'] = $appDir;
        $di->params[AppMeta::class]['tmpDir'] = $tmpDir;
    }

    private function responder(Container $di): void
    {
        $di->set(
            ResponderInterface::class,
            $di->lazy(
                static fn () => PHP_SAPI === 'cli' ?
                    $di->newInstance(CliResponder::class) :
                    $di->newInstance(WebResponder::class),
            )
        );
    }

    private function queryLocator(Container $di, string $appDir): void
    {
        $di->params[QueryLocator::class]['sqlDir'] = $appDir . '/var/sql';
    }

    private function requestDispatcher(Container $di): void
    {
        $di->params[CliRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);
        $di->params[WebRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);

        $di->params[RequestDispatcher::class]['appMeta'] = $di->lazyNew(AppMeta::class);
        $di->params[RequestDispatcher::class]['di'] = $di->lazy(fn () => $di);
        $di->params[RequestDispatcher::class]['router'] = $di->lazy(
            static fn () => PHP_SAPI === 'cli' ?
                $di->newInstance(CliRouter::class) :
                $di->newInstance(WebRouter::class)
        );
    }

    private function router(Container $di, string $appDir): void
    {
        $di->set(
            RouterContainer::class,
            $di->lazy(
                static function () use (&$appDir, $di) {
                    $router = $di->newInstance(RouterContainer::class);

                    $file = PHP_SAPI === 'cli' ?
                        $appDir . '/var/conf/aura.route.cli.php' :
                        $appDir . '/var/conf/aura.route.web.php';

                    if (file_exists($file)) {
                        $map = $router->getMap();
                        require $file;
                    }

                    return $router;
                }
            )
        );
    }
}
