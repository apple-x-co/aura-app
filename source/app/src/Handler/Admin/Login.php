<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\RequestHandler;

final class Login extends RequestHandler
{
    public function onGet(): self
    {
        return $this;
    }
}
