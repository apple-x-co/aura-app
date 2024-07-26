<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use Aura\Auth\Exception\MultipleMatches as AuraMultipleMatches;
use Aura\Auth\Exception\PasswordIncorrect as AuraPasswordIncorrect;
use Aura\Auth\Exception\PasswordMissing as AuraPasswordMissing;
use Aura\Auth\Exception\UsernameMissing as AuraUsernameMissing;
use Aura\Auth\Exception\UsernameNotFound as AuraUsernameNotFound;
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
        if ($this->isLogin($routerMatch)) {
            $body = (array) $routerMatch->serverRequest->getParsedBody();
            $username = $body['username'] ?? '';
            $password = $body['password'] ?? '';
            try {
                $this->adminAuthenticator->login($username, $password);
            } catch (Throwable $throwable) {
                $class = match ($throwable::class) {
                    AuraUsernameMissing::class => UsernameMissing::class,
                    AuraPasswordMissing::class => PasswordMissing::class,
                    AuraUsernameNotFound::class => UsernameNotFound::class,
                    AuraMultipleMatches::class => MultipleMatches::class,
                    AuraPasswordIncorrect::class => PasswordIncorrect::class,
                    default => null,
                };

                if ($class === null) {
                    throw $throwable;
                }

                throw new $class();
            }

            return new RedirectResponse($this->adminAuthenticator->getAuthRedirect());
        }

        if (! $this->isAdmin($routerMatch)) {
            return null;
        }

        $isValid = $this->adminAuthenticator->isValid();
        if (! $isValid) {
            return new RedirectResponse($this->adminAuthenticator->getUnauthRedirect());
        }

        if ($this->isLogout($routerMatch)) {
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

    private function isLogin(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['login']) &&
            is_bool($auth['login']) &&
            $auth['login'];
    }

    private function isLogout(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['logout']) &&
            is_bool($auth['logout']) &&
            $auth['logout'];
    }
}
