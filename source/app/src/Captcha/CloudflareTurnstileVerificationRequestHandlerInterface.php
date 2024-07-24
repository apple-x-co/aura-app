<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

interface CloudflareTurnstileVerificationRequestHandlerInterface
{
    public function onCfTurnstileFailed(CaptchaException $captchaException): self;
}
