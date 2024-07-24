<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Psr\Http\Message\ServerRequestInterface;

interface FormValidationInterface
{
    public function formValidate(ServerRequestInterface $serverRequest): bool;

    public function onFormValidationFailed(): self;
}
