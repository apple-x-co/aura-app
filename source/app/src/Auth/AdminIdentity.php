<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

class AdminIdentity
{
    public function __construct(
        public readonly int $id,
        public readonly string $displayName,
    ) {
    }
}
