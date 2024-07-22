<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use AppCore\Domain\Hasher\PasswordHasherInterface;
use Aura\Auth\Adapter\PdoAdapter;
use Aura\Auth\Auth;
use Aura\Auth\AuthFactory;
use Aura\Auth\Service\LoginService;
use Aura\Auth\Service\LogoutService;
use Aura\Auth\Service\ResumeService;
use Aura\Auth\Verifier\PasswordVerifier;
use MyVendor\MyProject\Auth\UnauthorizedException;
use PDO;

use function assert;
use function ini_get;
use function is_int;
use function is_string;

/** Admin 認証基盤 */
class AdminAuthenticator implements AdminAuthenticatorInterface
{
    private AuthFactory|null $authFactory = null;

    public function __construct(
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly string $pdoDsn,
        private readonly string $pdoUsername,
        private readonly string $pdoPassword,
    ) {
    }

    private function getAuthFactory(): AuthFactory
    {
        if ($this->authFactory !== null) {
            return $this->authFactory;
        }

        $this->authFactory = new AuthFactory($_COOKIE);

        return $this->authFactory;
    }

    private function adapter(): PdoAdapter
    {
        return $this->getAuthFactory()->newPdoAdapter(
            new PDO($this->pdoDsn, $this->pdoUsername, $this->pdoPassword),
            new PasswordVerifier($this->passwordHasher->hashType()),
            [
                '`admins`.`username`',
                '`admins`.`password`',
                '`admins`.`id`', // as UserData[0]
                '`admins`.`name` AS `display_name`', // as UserData[1]
            ],
            '`admins`',
        );
    }

    private function auth(): Auth
    {
        return $this->getAuthFactory()->newInstance();
    }

    private function loginService(): LoginService
    {
        return $this->getAuthFactory()->newLoginService($this->adapter());
    }

    private function logoutService(): LogoutService
    {
        return $this->getAuthFactory()->newLogoutService($this->adapter());
    }

    private function resumeService(): ResumeService
    {
        $sessionGcMaxLifeTime = (int) ini_get('session.gc_maxlifetime');

        return $this->getAuthFactory()->newResumeService($this->adapter(), $sessionGcMaxLifeTime);
    }

    public function login(string $username, string $password): void
    {
        $auth = $this->auth();
        $loginService = $this->loginService();
        $loginService->login(
            $auth,
            ['username' => $username, 'password' => $password],
        );
    }

    public function verifyPassword(string $username, string $password): void
    {
        $pdoAdapter = $this->adapter();
        $pdoAdapter->login(['username' => $username, 'password' => $password]);
    }

    public function logout(): void
    {
        $this->logoutService()->forceLogout($this->auth());
    }

    private function resume(): Auth
    {
        $auth = $this->auth();
        $this->resumeService()->resume($auth);

        return $auth;
    }

    public function isValid(): bool
    {
        return $this->resume()->isValid();
    }

    public function isExpired(): bool
    {
        return $this->resume()->isExpired();
    }

    public function getUserName(): string|null
    {
        return $this->auth()->getUserName();
    }

    public function getDisplayName(): string|null
    {
        $userData = $this->getUserData();

        return $userData['display_name'] ?? null;
    }

    public function getUserId(): int|null
    {
        $userData = $this->getUserData();
        if (isset($userData['id'])) {
            $adminId = (int) $userData['id'];
            assert($adminId > 0);

            return $adminId;
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getUserData(): array
    {
        return $this->auth()->getUserData();
    }

    public function getAuthRedirect(): string
    {
        return '/admin/index';
    }

    public function getUnauthRedirect(): string
    {
        return '/admin/login';
    }

    public function getPasswordRedirect(): string
    {
        return '/admin/password-confirm';
    }

    public function getIdentity(): AdminIdentity
    {
        $auth = $this->auth();
        if (! $auth->isValid()) {
            throw new UnauthorizedException();
        }

        $userData = $auth->getUserData();
        assert(isset($userData['id']) && is_int($userData['id']));
        assert(isset($userData['display_name']) && is_string($userData['display_name']));

        return new AdminIdentity(
            $userData['id'],
            $userData['display_name'],
        );
    }
}
