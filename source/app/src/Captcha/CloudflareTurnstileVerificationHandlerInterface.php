<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

interface CloudflareTurnstileVerificationHandlerInterface
{
    public function __invoke(): void;
}
