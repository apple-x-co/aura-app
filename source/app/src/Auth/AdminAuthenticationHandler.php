<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use Laminas\Diactoros\Response\RedirectResponse;
use MyVendor\MyPackage\Router\RouterMatch;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function is_array;
use function is_bool;

final class AdminAuthenticationHandler implements AdminAuthenticationHandlerInterface
{
    public function __construct(
        private readonly AdminAuthenticatorInterface $adminAuthenticator,
    ) {
    }

    public function __invoke(RouterMatch $routerMatch): ResponseInterface|null
    {
        if (
            ! $this->isAdmin($routerMatch) ||
            $this->isGetLogin($routerMatch)
        ) {
            return null;
        }

        if ($this->isPostLogin($routerMatch)) {
            try {
                $this->adminAuthenticator->login('admin', 'p@ssw0rd'); // TODO
            } catch (Throwable) {
                return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
            }

            return new RedirectResponse($this->adminAuthenticator->getAuthRedirect());
        }

        $isValid = $this->adminAuthenticator->isValid();
        if (! $isValid) {
            return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
        }

        if ($this->isPostLogout($routerMatch)) {
            $this->adminAuthenticator->logout();

            return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
        }

        return null;
    }

    private function isAdmin(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['admin']) &&
            is_bool($auth['admin']) &&
            $auth['admin'];
    }

    private function isGetLogin(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['adminLogin']) &&
            is_bool($auth['adminLogin']) &&
            $auth['adminLogin'] &&
            $routerMatch->method === 'GET';
    }

    private function isPostLogin(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['adminLogin']) &&
            is_bool($auth['adminLogin']) &&
            $auth['adminLogin'] &&
            $routerMatch->method === 'POST';
    }

    private function isPostLogout(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['adminLogout']) &&
            is_bool($auth['adminLogout']) &&
            $auth['adminLogout'] &&
            $routerMatch->method === 'POST';
    }
}
