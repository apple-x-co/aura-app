<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\RequestHandler;

use Koriym\HttpConstants\StatusCode;
use MyVendor\MyPackage\Renderer\RendererInterface;

abstract class AbstractRequestHandler
{
    /** @var int<200, max>  */
    public int $code = StatusCode::OK;

    /** @var array<string, mixed>  */
    public array $headers = [];

    /** @var array<string, mixed>  */
    public array $body = [];

    public RendererInterface|null $renderer = null;

    public string|null $string = null;

    public function setRenderer(RendererInterface|null $renderer): void
    {
        $this->renderer = $renderer;
    }
}
