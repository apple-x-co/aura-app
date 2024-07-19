<?php

declare(strict_types=1);

namespace AppCore\Domain\Hasher;

use function password_hash;
use function password_verify;

use const PASSWORD_BCRYPT;

class PasswordHasher implements PasswordHasherInterface
{
    public function hashType(): string
    {
        return PASSWORD_BCRYPT;
    }

    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verify(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
}
