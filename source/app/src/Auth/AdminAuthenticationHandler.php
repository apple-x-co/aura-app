<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use Aura\Router\Route;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

use function is_array;
use function is_bool;

final class AdminAuthenticationHandler implements AdminAuthenticationHandlerInterface
{
    public function __construct(
        private readonly AdminAuthenticatorInterface $adminAuthenticator,
    ) {
    }

    public function __invoke(Route $route): ResponseInterface|null
    {
        if (! $this->isAdminGuard($route)) {
            return null;
        }

        if ($this->adminAuthenticator->isValid()) {
            return null;
        }

        return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
    }

    private function isAdminGuard(Route $route): bool
    {
        return is_array($route->auth) &&
            isset($route->auth['admin']) &&
            is_bool($route->auth['admin']) &&
            $route->auth['admin'];
    }
}
