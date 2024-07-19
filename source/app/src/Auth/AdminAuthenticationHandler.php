<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use Laminas\Diactoros\Response\RedirectResponse;
use MyVendor\MyPackage\Router\RouterMatch;
use Psr\Http\Message\ResponseInterface;

use Throwable;

use function is_array;
use function is_bool;
use function var_dump;

final class AdminAuthenticationHandler implements AdminAuthenticationHandlerInterface
{
    public function __construct(
        private readonly AdminAuthenticatorInterface $adminAuthenticator,
    ) {
    }

    public function __invoke(RouterMatch $routerMatch): ResponseInterface|null
    {
        if ($this->isLogin($routerMatch)) {
            try {
                $this->adminAuthenticator->login('admin', 'p@ssw0rd');
            } catch (Throwable) {
                return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
            }

            return new RedirectResponse($this->adminAuthenticator->getAuthRedirect());
        }

        if (! $this->isGuard($routerMatch)) {
            return null;
        }

        $isValid = $this->adminAuthenticator->isValid();

        if ($isValid && $this->isLogout($routerMatch)) {
            $this->adminAuthenticator->logout();

            return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
        }

        if ($isValid) {
            return null;
        }

        return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
    }

    private function isLogin(RouterMatch $routerMatch): bool
    {
        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['adminLogin']) &&
            is_bool($auth['adminLogin']) &&
            $auth['adminLogin'] &&
            $routerMatch->method === 'POST';
    }

    private function isLogout(RouterMatch $routerMatch): bool
    {
        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['adminLogout']) &&
            is_bool($auth['adminLogout']) &&
            $auth['adminLogout'] &&
            $routerMatch->method === 'POST';
    }

    private function isGuard(RouterMatch $routerMatch): bool
    {
        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['admin']) &&
            is_bool($auth['admin']) &&
            $auth['admin'];
    }
}
