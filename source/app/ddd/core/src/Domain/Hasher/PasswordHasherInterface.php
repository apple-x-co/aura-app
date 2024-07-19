<?php

declare(strict_types=1);

namespace AppCore\Domain\Hasher;

interface PasswordHasherInterface
{
    public function hashType(): string;

    public function hash(string $password): string;

    public function verify(string $password, string $hashedPassword): bool;
}
