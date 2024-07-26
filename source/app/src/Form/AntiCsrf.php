<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;
use Aura\Session\CsrfToken;
use Aura\Session\Session;

use function assert;
use function is_string;

final class AntiCsrf implements AntiCsrfInterface
{
    public const INPUT_NAME = '__csrf_token';

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
        $fieldset->setField(self::INPUT_NAME, 'hidden')
            ->setAttribs(['value' => $this->getCsrfToken()->getValue()]);
    }

    /** @param array<array-key, mixed> $data */
    public function isValid(array $data): bool
    {
        if (! isset($data[self::INPUT_NAME])) {
            return false;
        }

        assert(is_string($data[self::INPUT_NAME]));

        return $this->getCsrfToken()->isValid($data[self::INPUT_NAME]);
    }
}
