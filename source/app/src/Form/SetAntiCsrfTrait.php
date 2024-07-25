<?php

namespace MyVendor\MyPackage\Form;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfTrait
{
    /**
     * @param AntiCsrfInterface $antiCsrf
     *
     * @see \Aura\Input\Form::$anti_csrf
     */
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf): void
    {
        $this->anti_csrf = $antiCsrf;
        $this->anti_csrf->setField($this);
    }
}
