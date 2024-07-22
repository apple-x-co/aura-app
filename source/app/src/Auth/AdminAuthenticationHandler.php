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
        if (
            ! $this->isAdmin($routerMatch) ||
            $this->isGetLogin($routerMatch)
        ) {
            return null;
        }

        if ($this->isPostLogin($routerMatch)) {
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
