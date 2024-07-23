<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\TemplateEngine;

use Qiq\Helper\Html\HtmlHelpers;

final class QiqCustomHelper extends HtmlHelpers
{
    public function __construct(
        private readonly string $cloudflareTurnstileSiteKey,
    ) {
        parent::__construct();
    }

    /** @see https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/ */
    public function cfTurnstileWidget(
        string $size = 'normal',
        string $action = 'none',
        string|null $checked = null,
        string|null $expired = null,
        string|null $error = null,
        string|null $timeout = null,
    ): string {
        $attribs = [
            'class' => 'cf-turnstile',
            'data-action' => $action,
            'data-language' => 'ja',
            'data-sitekey' => $this->cloudflareTurnstileSiteKey,
            'data-size' => $size,
            'data-theme' => 'light',
        ];

        if (is_string($checked)) {
            $attribs['data-callback'] = $checked;
        }

        if (is_string($expired)) {
            $attribs['data-expired-callback'] = $expired;
        }

        if (is_string($error)) {
            $attribs['data-error-callback'] = $error;
        }

        if (is_string($timeout)) {
            $attribs['data-timeout-callback'] = $timeout;
        }

        return sprintf('<div %s></div>', $this->a($attribs));
    }
}
