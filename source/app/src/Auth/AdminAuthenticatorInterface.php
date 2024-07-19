<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

interface AdminAuthenticatorInterface
{
    public function login(string $username, string $password): void;

    public function verifyPassword(string $username, string $password): void;

    public function logout(): void;

    public function isValid(): bool;

    public function isExpired(): bool;

    public function getUserName(): string|null;

    /** @return  int<1, max>|null */
    public function getUserId(): int|null;

    public function getDisplayName(): string|null;

    /**
     * @return array{id?: string, display_name?: string}
     *
     * @internal
     */
    public function getUserData(): array;

    public function getAuthRedirect(): string;

    public function getUnauthRedirect(): string;

    public function getPasswordRedirect(): string;

    public function getIdentity(): AdminIdentity;
}
