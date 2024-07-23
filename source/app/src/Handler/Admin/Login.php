<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\Auth\AuthenticationException;
use MyVendor\MyPackage\Captcha\CaptchaException;
use MyVendor\MyPackage\RequestHandler;

final class Login extends RequestHandler
{
    public function onGet(): self
    {
        return $this;
    }

    public function onAuthenticationFailed(AuthenticationException $authenticationException): self
    {
        $this->body['authError'] = true;

        return $this;
    }

    public function onCfTurnstileFailed(CaptchaException $captchaException): self
    {
        $this->body['captchaError'] = true;

        return $this;
    }
}
