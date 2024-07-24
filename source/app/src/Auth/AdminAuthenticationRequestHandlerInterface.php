<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

interface AdminAuthenticationRequestHandlerInterface
{
    public function onAuthenticationFailed(AuthenticationException $authenticationException): self;
}
