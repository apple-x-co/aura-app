<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use AppCore\Application\Shared\DbConnectionInterface;
use AppCore\Domain\Hasher\PasswordHasher;
use AppCore\Infrastructure\Persistence\DbConnection;
use Aura\Accept\Accept;
use Aura\Accept\AcceptFactory;
use Aura\Di\Container;
use Aura\Di\ContainerBuilder;
use Aura\Html\HelperLocator;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;
use Aura\Input\Filter;
use Aura\Router\RouterContainer;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use Aura\SqlQuery\Common\DeleteInterface;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\QueryFactory;
use Koriym\QueryLocator\QueryLocator;
use Koriym\QueryLocator\QueryLocatorInterface;
use MyVendor\MyPackage\Auth\AdminAuthenticationHandler;
use MyVendor\MyPackage\Auth\AdminAuthenticator;
use MyVendor\MyPackage\Auth\AdminAuthenticatorInterface;
use MyVendor\MyPackage\Captcha\CloudflareTurnstileVerificationHandler;
use MyVendor\MyPackage\Form\Admin\LoginForm;
use MyVendor\MyPackage\Form\AntiCsrf;
use MyVendor\MyPackage\Form\ExtendedForm;
use MyVendor\MyPackage\Form\SetAntiCsrfInject;
use MyVendor\MyPackage\Renderer\HtmlRenderer;
use MyVendor\MyPackage\Renderer\JsonRenderer;
use MyVendor\MyPackage\Renderer\TextRenderer;
use MyVendor\MyPackage\Responder\CliResponder;
use MyVendor\MyPackage\Responder\ResponderInterface;
use MyVendor\MyPackage\Responder\WebResponder;
use MyVendor\MyPackage\Router\CliRouter;
use MyVendor\MyPackage\Router\RouterInterface;
use MyVendor\MyPackage\Router\ServerRequestFactory;
use MyVendor\MyPackage\Router\WebRouter;
use MyVendor\MyPackage\TemplateEngine\QiqCustomHelper;
use MyVendor\MyPackage\TemplateEngine\QiqRenderer;
use Psr\Http\Message\ServerRequestInterface;
use Qiq\Template;

use function assert;
use function file_exists;
use function getenv;
use function is_string;
use function time;

use const PHP_SAPI;

/** @psalm-suppress UndefinedPropertyAssignment */
final class DiBinder
{
    public function __invoke(string $appDir, string $tmpDir): Container
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance(true); // NOTE: "$di->types['xxx']" を使うために有効化

        $di->values['timestamp'] = time();
        $di->values['siteUrl'] = getenv('SITE_URL');
        $di->values['pdoDsn'] = getenv('DB_DSN');
        $di->values['pdoUsername'] = getenv('DB_USER');
        $di->values['pdoPassword'] = getenv('DB_PASS');

        $this->appMeta($di, $appDir, $tmpDir);
        $this->authentication($di);
        $this->db($di, $appDir);
        $this->form($di);
        $this->renderer($di, $appDir);
        $this->request($di);
        $this->requestDispatcher($di);
        $this->responder($di);
        $this->router($di, $appDir);
        $this->security($di);
        $this->session($di);

        return $di;
    }

    private function appMeta(Container $di, string $appDir, string $tmpDir): void
    {
        $di->values['appDir'] = $appDir;
        $di->values['tmpDir'] = $tmpDir;

        $di->params[AppMeta::class]['appDir'] = $di->lazyValue('appDir');
        $di->params[AppMeta::class]['tmpDir'] = $di->lazyValue('tmpDir');

        $di->set(AppMeta::class, $di->lazyNew(AppMeta::class));
    }

    private function authentication(Container $di): void
    {
        $di->params[AdminAuthenticator::class]['passwordHasher'] = $di->lazyNew(PasswordHasher::class);
        $di->params[AdminAuthenticator::class]['pdoDsn'] = $di->lazyValue('pdoDsn');
        $di->params[AdminAuthenticator::class]['pdoUsername'] = $di->lazyValue('pdoUsername');
        $di->params[AdminAuthenticator::class]['pdoPassword'] = $di->lazyValue('pdoPassword');
        $di->set(AdminAuthenticator::class, $di->lazyNew(AdminAuthenticator::class));

        $di->params[AdminAuthenticationHandler::class]['adminAuthenticator'] = $di->lazyGet(AdminAuthenticator::class);

        $di->types[AdminAuthenticatorInterface::class] = $di->lazyGet(AdminAuthenticator::class);
    }

    private function db(Container $di, string $appDir): void
    {
        $di->params[ExtendedPdo::class]['dsn'] = $di->lazyValue('pdoDsn');
        $di->params[ExtendedPdo::class]['username'] = $di->lazyValue('pdoUsername');
        $di->params[ExtendedPdo::class]['password'] = $di->lazyValue('pdoPassword');
        $di->set(ExtendedPdoInterface::class, $di->lazyNew(ExtendedPdo::class));
        $di->types[ExtendedPdoInterface::class] = $di->lazyGet(ExtendedPdoInterface::class);

        $di->types[SelectInterface::class] = $di->lazy(static fn () => (new QueryFactory('mysql'))->newSelect());
        $di->types[InsertInterface::class] = $di->lazy(static fn () => (new QueryFactory('mysql'))->newInsert());
        $di->types[UpdateInterface::class] = $di->lazy(static fn () => (new QueryFactory('mysql'))->newUpdate());
        $di->types[DeleteInterface::class] = $di->lazy(static fn () => (new QueryFactory('mysql'))->newDelete());

        $di->types[DbConnectionInterface::class] = $di->lazyNew(DbConnection::class);

        $di->values['sqlDir'] = $appDir . '/var/sql';
        $di->params[QueryLocator::class]['sqlDir'] = $di->lazyValue('sqlDir');

        $di->set(QueryLocatorInterface::class, $di->lazyNew(QueryLocator::class));

        $di->types[QueryLocatorInterface::class] = $di->lazyGet(QueryLocatorInterface::class);
    }

    private function form(Container $di): void
    {
        $di->params[ExtendedForm::class]['builder'] = $di->lazyNew(Builder::class);
        $di->params[ExtendedForm::class]['filter'] = $di->lazyNew(Filter::class);

        $di->set(HelperLocator::class, $di->lazy(static fn () => (new HelperLocatorFactory())->newInstance()));
        $di->params[ExtendedForm::class]['helper'] = $di->lazyGet(HelperLocator::class);

        $di->setters[SetAntiCsrfInject::class]['setAntiCsrf'] = $di->lazyNew(AntiCsrf::class);

        $di->types[LoginForm::class] = $di->lazyNew(LoginForm::class);
    }

    private function renderer(Container $di, string $appDir): void
    {
        $di->params[QiqRenderer::class]['template'] = $di->lazy(static function () use ($appDir, $di) {
            $qiqCachePath = getenv('QIQ_CACHE_PATH');

            $helper = $di->newInstance(QiqCustomHelper::class);
            assert($helper instanceof QiqCustomHelper);

            return Template::new(
                [$appDir . '/var/qiq/template'],
                '.php',
                is_string($qiqCachePath) && $qiqCachePath !== '' ? $appDir . $qiqCachePath : null,
                $helper,
            );
        });
        $di->params[QiqRenderer::class]['data'] = $di->lazyArray([
            'siteUrl' => $di->lazyValue('siteUrl'),
            'timestamp' => $di->lazyValue('timestamp'),
        ]);
        $di->set(QiqRenderer::class, $di->lazyNew(QiqRenderer::class));

        $di->params[QiqCustomHelper::class]['cloudflareTurnstileSiteKey'] = $di->lazyValue('cloudflareTurnstileSiteKey');

        $di->params[HtmlRenderer::class]['qiqRenderer'] = $di->lazyGet(QiqRenderer::class);

        $di->set(HtmlRenderer::class, $di->lazyNew(HtmlRenderer::class));
        $di->set(JsonRenderer::class, $di->lazyNew(JsonRenderer::class));
        $di->set(TextRenderer::class, $di->lazyNew(TextRenderer::class));

        $di->types[HtmlRenderer::class] = $di->lazyGet(HtmlRenderer::class);
        $di->types[JsonRenderer::class] = $di->lazyGet(JsonRenderer::class);
        $di->types[TextRenderer::class] = $di->lazyGet(TextRenderer::class);
    }

    private function request(Container $di): void
    {
        $di->set(ServerRequestInterface::class, $di->lazy(static fn () => ServerRequestFactory::fromGlobals()));
        $di->types[ServerRequestInterface::class] = $di->lazyGet(ServerRequestInterface::class);

        $di->set(Accept::class, $di->lazy(static fn () => (new AcceptFactory($_SERVER))->newInstance()));
    }

    private function requestDispatcher(Container $di): void
    {
        $di->params[CliRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);
        $di->params[WebRouter::class]['routerContainer'] = $di->lazyGet(RouterContainer::class);

        $di->params[RequestDispatcher::class]['adminAuthenticationHandler'] = $di->lazyNew(AdminAuthenticationHandler::class);
        $di->params[RequestDispatcher::class]['di'] = $di->lazy(static fn () => $di);
        $di->params[RequestDispatcher::class]['accept'] = $di->lazyGet(Accept::class);

        $di->types[RouterInterface::class] = $di->lazyGet(RouterInterface::class);

        if (PHP_SAPI === 'cli') {
            $di->set(RouterInterface::class, $di->lazyNew(CliRouter::class));

            return;
        }

        $di->set(RouterInterface::class, $di->lazyNew(WebRouter::class));
    }

    private function responder(Container $di): void
    {
        if (PHP_SAPI === 'cli') {
            $di->set(ResponderInterface::class, $di->lazyNew(CliResponder::class));

            return;
        }

        $di->set(ResponderInterface::class, $di->lazyNew(WebResponder::class));
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

                        $adminPrefix = getenv('ADMIN_PREFIX');

                        /** @psalm-suppress UnresolvableInclude */
                        require $file;

                        return $router;
                    },
                ),
            );

            return;
        }

        $di->set(RouterContainer::class, $di->lazyNew(RouterContainer::class));
    }

    private function security(Container $di): void
    {
        $di->values['cloudflareTurnstileSecretKey'] = getenv('CLOUDFLARE_TURNSTILE_SECRET_KEY');
        $di->values['cloudflareTurnstileSiteKey'] = getenv('CLOUDFLARE_TURNSTILE_SITE_KEY');

        $di->params[CloudflareTurnstileVerificationHandler::class]['secretKey'] = $di->lazyValue('cloudflareTurnstileSecretKey');
    }

    private function session(Container $di): void
    {
        $di->set(Session::class, $di->lazy(static fn () => (new SessionFactory())->newInstance($_COOKIE)));
        // $di->types[Session::class] = $di->lazyGet(Session::class);

        $di->params[AntiCsrf::class]['session'] = $di->lazyGet(Session::class);
    }
}
