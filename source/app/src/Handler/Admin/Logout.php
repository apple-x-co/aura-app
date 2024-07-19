<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\RequestHandler;

final class Logout extends RequestHandler
{
    public function onPost(): self
    {
        return $this;
    }
}
