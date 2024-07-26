<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfInject
{
    /** @see \Aura\Input\Form::setAntiCsrf() */
    // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
    public function setAntiCsrf(AntiCsrfInterface $anti_csrf): void
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        parent::setAntiCsrf($anti_csrf);
    }
}
