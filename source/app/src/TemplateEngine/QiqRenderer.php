<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\TemplateEngine;

use MyVendor\MyPackage\AbstractRequestHandler;
use Qiq\Template;
use ReflectionClass;

use function array_merge;
use function assert;
use function is_array;
use function is_string;
use function str_replace;
use function strpos;
use function substr;

final class QiqRenderer
{
    private const LENGTH_OF_RESOURCE_DIR = 12;

    /** @param array<string, mixed> $data */
    public function __construct(
        private readonly Template $template,
        private readonly array $data,
    ) {
    }

    public function render(AbstractRequestHandler $requestHandler): string
    {
        $template = clone $this->template;
        $this->setTemplateView($template, $requestHandler);
        assert($requestHandler->body === null || is_array($requestHandler->body));
        $template->setData(array_merge($this->data, $requestHandler->body ?? []));

        return $template();
    }

    private function setTemplateView(Template $template, AbstractRequestHandler $requestHandler): void
    {
        $fileName = (new ReflectionClass($requestHandler))->getFileName();
        assert(is_string($fileName));

        $pos = strpos($fileName, 'src/Handler/');
        $relativePath = substr($fileName, (int) $pos + self::LENGTH_OF_RESOURCE_DIR);

        $view = str_replace('.php', '', $relativePath);
        $template->setView($view);
    }
}
