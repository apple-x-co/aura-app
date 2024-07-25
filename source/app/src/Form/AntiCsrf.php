<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;
use Aura\Session\CsrfToken;
use Aura\Session\Session;

final class AntiCsrf implements AntiCsrfInterface
{
    public const FIELD_NAME = '__csrf_token';

    public function __construct(
        private readonly Session $session,
    ) {
    }

    private function getCsrfToken(): CsrfToken
    {
        return $this->session->getCsrfToken();
    }

    public function setField(Fieldset $fieldset): void
    {
        $fieldset->setField(self::FIELD_NAME, 'hidden')
            ->setAttribs(['value' => $this->getCsrfToken()->getValue()]);
    }

    public function isValid(array $data): bool
    {
        if (! isset($data[self::FIELD_NAME])) {
            return false;
        }

        return $this->getCsrfToken()->isValid($data[self::FIELD_NAME]);
    }
}
