<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Koriym\HttpConstants\StatusCode;
use MyVendor\MyPackage\Renderer\RendererInterface;

abstract class AbstractRequestHandler
{
    /** @var int<200, max>  */
    public int $code = StatusCode::OK;

    /** @var array<string, mixed>  */
    public array $headers = [];

    /** @var array<string, mixed>|null  */
    public array|null $body = null;
    public RendererInterface|null $renderer = null;
    public string|null $string = null;
}
