<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\Auth\AuthenticationException;
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
}
