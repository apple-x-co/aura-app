<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

final class AppMeta
{
    public function __construct(
        public readonly string $appDir,
        public readonly string $tmpDir,
    ) {
    }
}
