<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Koriym\HttpConstants\StatusCode;
use MyVendor\MyPackage\Renderer\RendererInterface;
use Stringable;

class RequestHandler implements Stringable
{
    /** @var int<200, max>  */
    public int $code = StatusCode::OK;

    /** @var array<string, string>  */
    public array $headers = [];

    /** @var array<string, mixed>|null  */
    public array|null $body = null;

    /** @var resource|null */
    public $stream;
    public RendererInterface|null $renderer = null;
    public string|null $string = null;

    public function __toString(): string
    {
        return '';
    }
}
