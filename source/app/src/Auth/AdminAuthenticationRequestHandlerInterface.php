<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Auth;

use MyVendor\MyPackage\RequestHandler;

interface AdminAuthenticationRequestHandlerInterface
{
    public function onAuthenticationFailed(AuthenticationException $authenticationException): RequestHandler;
}
