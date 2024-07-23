<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

use MyVendor\MyPackage\Router\RouterMatch;

interface CloudflareTurnstileVerificationHandlerInterface
{
    public function __invoke(RouterMatch $routerMatch): void;
}
