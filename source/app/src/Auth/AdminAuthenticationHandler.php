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
        $isAdmin = is_array($route->auth) &&
            isset($route->auth['admin']) &&
            is_bool($route->auth['admin']) &&
            $route->auth['admin'];

        if ($isAdmin && ! $this->adminAuthenticator->isValid()) {
            return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
        }

        return null;
    }
}
