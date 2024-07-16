<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use A;
use Aura\Di\Container;
use Aura\Di\ContainerBuilder;
use Aura\Router\RouterContainer;
use Koriym\QueryLocator\QueryLocator;
use MyVendor\MyPackage\Renderer\HtmlRenderer;
use MyVendor\MyPackage\Responder\CliResponder;
use MyVendor\MyPackage\Responder\ResponderInterface;
use MyVendor\MyPackage\Responder\WebResponder;
use MyVendor\MyPackage\Router\CliRouter;
use MyVendor\MyPackage\Router\RouterInterface;
use MyVendor\MyPackage\Router\WebRouter;
use MyVendor\MyPackage\TemplateEngine\QiqRenderer;
use Qiq\Template;

use function file_exists;

use function getenv;
use function time;

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
        $this->renderer($di, $appDir);

        return $di;
    }

    private function appMeta(Container $di, string $appDir, string $tmpDir): void
    {
        $di->values['timestamp'] = time();
        $di->values['appDir'] = $appDir;
        $di->values['tmpDir'] = $tmpDir;

        $di->params[AppMeta::class]['appDir'] = $di->lazyValue('appDir');
        $di->params[AppMeta::class]['tmpDir'] = $di->lazyValue('tmpDir');

        $di->set(AppMeta::class, $di->lazyNew(AppMeta::class));
    }

    private function responder(Container $di): void
    {
        if (PHP_SAPI === 'cli') {
            $di->set(ResponderInterface::class, $di->lazyNew(CliResponder::class));

            return;
        }

        $di->set(ResponderInterface::class, $di->lazyNew(WebResponder::class));
    }

    private function queryLocator(Container $di, string $appDir): void
    {
        $di->values['sqlDir'] = $appDir . '/var/sql';

        $di->params[QueryLocator::class]['sqlDir'] = $di->lazyValue('sqlDir');
    }

    private function requestDispatcher(Container $di): void
    {
        $di->params[CliRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);
        $di->params[WebRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);

        $di->params[RequestDispatcher::class]['appMeta'] = $di->lazyGet(AppMeta::class);
        $di->params[RequestDispatcher::class]['di'] = $di->lazy(fn () => $di);
        $di->params[RequestDispatcher::class]['router'] = $di->lazyGet(RouterInterface::class);

        if (PHP_SAPI === 'cli') {
            $di->set(RouterInterface::class, $di->lazyNew(CliRouter::class));

            return;
        }

        $di->set(RouterInterface::class, $di->lazyNew(WebRouter::class));
    }

    private function router(Container $di, string $appDir): void
    {
        $file = PHP_SAPI === 'cli' ?
            $appDir . '/var/conf/aura.route.cli.php' :
            $appDir . '/var/conf/aura.route.web.php';

        if (file_exists($file)) {
            $di->set(
                RouterContainer::class,
                $di->lazy(
                    static function () use ($file) {
                        $router = new RouterContainer();
                        $map = $router->getMap();
                        require $file;

                        return $router;
                    }
                )
            );

            return;
        }

        $di->set(RouterContainer::class, $di->lazyNew(RouterContainer::class));
    }

    private function renderer(Container $di, string $appDir): void
    {
        $qiqCachePath = getenv('QIQ_CACHE_PATH');

        $di->params[QiqRenderer::class]['template'] = $di->lazy(fn () => Template::new(
            [$appDir . '/var/qiq/template'],
            '.php',
            empty($qiqCachePath) ? null : $appDir . $qiqCachePath,
        ));
        $di->params[QiqRenderer::class]['data'] = $di->lazyArray([
            'timestamp' => $di->lazyValue('timestamp'),
        ]);

        $di->params[HtmlRenderer::class]['qiqRenderer'] = $di->lazyNew(QiqRenderer::class);
    }
}
