<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Auth;

use BEAR\Resource\Code;
use BEAR\Resource\Exception\BadRequestException;
use Throwable;

class ForbiddenException extends BadRequestException
{
    public function __construct(string $message = '', int $code = Code::FORBIDDEN, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
