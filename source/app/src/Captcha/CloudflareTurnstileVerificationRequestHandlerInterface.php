<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

use MyVendor\MyPackage\RequestHandler;

interface CloudflareTurnstileVerificationRequestHandlerInterface
{
    public function onCfTurnstileFailed(CaptchaException $captchaException): RequestHandler;
}
